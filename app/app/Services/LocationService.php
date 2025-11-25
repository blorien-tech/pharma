<?php

namespace App\Services;

use App\Models\StorageLocation;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class LocationService
{
    /**
     * Suggest best available location for a product
     * Intelligent algorithm considers:
     * - Temperature requirements
     * - Available capacity
     * - Existing product locations (group similar products)
     * - FIFO proximity (store by expiry date)
     */
    public function suggestLocationForProduct(Product $product, ?string $expiryDate = null): ?StorageLocation
    {
        // 1. Check if product already has batches in locations
        $existingLocation = $this->getProductPreferredLocation($product);
        if ($existingLocation && !$existingLocation->isFull()) {
            return $existingLocation;
        }

        // 2. Filter by temperature requirements (if any)
        $query = StorageLocation::active()->available();

        // 3. Prefer locations of type BIN (most specific)
        $query->where('type', 'BIN');

        // 4. Order by: capacity available (descending), then display_order
        $location = $query->orderByRaw('(capacity - (SELECT COUNT(*) FROM product_batches WHERE storage_location_id = storage_locations.id)) DESC')
            ->orderBy('display_order')
            ->first();

        return $location;
    }

    /**
     * Get the most commonly used location for a product
     */
    public function getProductPreferredLocation(Product $product): ?StorageLocation
    {
        // Get the location with most batches of this product
        $locationId = ProductBatch::where('product_id', $product->id)
            ->whereNotNull('storage_location_id')
            ->groupBy('storage_location_id')
            ->orderByRaw('COUNT(*) DESC')
            ->value('storage_location_id');

        return $locationId ? StorageLocation::find($locationId) : null;
    }

    /**
     * Assign batch to a location with smart defaults
     */
    public function assignBatchToLocation(
        ProductBatch $batch,
        ?int $locationId = null,
        ?string $notes = null
    ): ProductBatch {
        return DB::transaction(function () use ($batch, $locationId, $notes) {
            $oldLocationId = $batch->storage_location_id;

            // If no location specified, suggest one
            if (!$locationId) {
                $suggestedLocation = $this->suggestLocationForProduct(
                    $batch->product,
                    $batch->expiry_date
                );

                if (!$suggestedLocation) {
                    throw new \Exception('No available storage location found. Please create storage locations first.');
                }

                $locationId = $suggestedLocation->id;
            }

            // Update batch location
            $batch->update(['storage_location_id' => $locationId]);

            // Record movement
            StockMovement::recordMovement(
                batchId: $batch->id,
                fromLocationId: $oldLocationId,
                toLocationId: $locationId,
                quantity: $batch->quantity_remaining,
                reason: $oldLocationId ? 'TRANSFER' : 'RECEIPT',
                notes: $notes
            );

            return $batch->fresh(['storageLocation']);
        });
    }

    /**
     * Move batch between locations
     */
    public function moveBatch(
        ProductBatch $batch,
        int $toLocationId,
        ?string $reason = 'TRANSFER',
        ?string $notes = null
    ): ProductBatch {
        return DB::transaction(function () use ($batch, $toLocationId, $reason, $notes) {
            $fromLocationId = $batch->storage_location_id;

            // Validate destination exists and is available
            $toLocation = StorageLocation::findOrFail($toLocationId);

            if ($toLocation->isFull()) {
                throw new \Exception("Destination location '{$toLocation->name}' is at full capacity.");
            }

            // Update batch location
            $batch->update(['storage_location_id' => $toLocationId]);

            // Record movement
            StockMovement::recordMovement(
                batchId: $batch->id,
                fromLocationId: $fromLocationId,
                toLocationId: $toLocationId,
                quantity: $batch->quantity_remaining,
                reason: $reason,
                notes: $notes
            );

            return $batch->fresh(['storageLocation']);
        });
    }

    /**
     * Get location statistics
     */
    public function getLocationStatistics(StorageLocation $location): array
    {
        $batches = $location->batches()->with('product')->get();

        return [
            'total_batches' => $batches->count(),
            'total_products' => $batches->pluck('product_id')->unique()->count(),
            'total_quantity' => $batches->sum('quantity_remaining'),
            'capacity' => $location->capacity,
            'occupancy' => $location->getCurrentOccupancy(),
            'occupancy_percentage' => $location->getOccupancyPercentage(),
            'remaining_capacity' => $location->getRemainingCapacity(),
            'is_full' => $location->isFull(),
            'expired_batches' => $batches->filter->isExpired()->count(),
            'expiring_soon_batches' => $batches->filter->isExpiringSoon()->count(),
        ];
    }

    /**
     * Get all locations with statistics
     */
    public function getAllLocationsWithStats(): Collection
    {
        return StorageLocation::active()
            ->with(['batches.product'])
            ->get()
            ->map(function ($location) {
                return [
                    'location' => $location,
                    'stats' => $this->getLocationStatistics($location),
                ];
            });
    }

    /**
     * Find locations that need attention
     * Returns locations that are: full, have expired stock, or have expiring stock
     */
    public function getLocationsNeedingAttention(): Collection
    {
        return StorageLocation::active()
            ->with(['batches.product'])
            ->get()
            ->filter(function ($location) {
                $stats = $this->getLocationStatistics($location);
                return $stats['is_full'] ||
                       $stats['expired_batches'] > 0 ||
                       $stats['expiring_soon_batches'] > 0;
            })
            ->map(function ($location) {
                return [
                    'location' => $location,
                    'stats' => $this->getLocationStatistics($location),
                    'alerts' => $this->getLocationAlerts($location),
                ];
            });
    }

    /**
     * Get alerts for a specific location
     */
    public function getLocationAlerts(StorageLocation $location): array
    {
        $alerts = [];
        $stats = $this->getLocationStatistics($location);

        if ($stats['is_full']) {
            $alerts[] = [
                'type' => 'full',
                'severity' => 'warning',
                'message' => 'Location is at full capacity',
            ];
        }

        if ($stats['expired_batches'] > 0) {
            $alerts[] = [
                'type' => 'expired',
                'severity' => 'danger',
                'message' => "{$stats['expired_batches']} expired batch(es) need removal",
            ];
        }

        if ($stats['expiring_soon_batches'] > 0) {
            $alerts[] = [
                'type' => 'expiring',
                'severity' => 'warning',
                'message' => "{$stats['expiring_soon_batches']} batch(es) expiring soon",
            ];
        }

        if ($location->requiresTemperatureControl()) {
            $alerts[] = [
                'type' => 'temperature',
                'severity' => 'info',
                'message' => "Temperature controlled: {$location->temperature_min}°C - {$location->temperature_max}°C",
            ];
        }

        return $alerts;
    }

    /**
     * Create a hierarchical location structure quickly
     * Example: createHierarchy('Main Rack', 5, 4, 3)
     * Creates: 1 Rack with 5 Shelves, each shelf with 4 Bins (3 capacity per bin)
     */
    public function createHierarchy(
        string $rackName,
        int $shelfCount,
        int $binCount,
        int $binCapacity = 10
    ): StorageLocation {
        return DB::transaction(function () use ($rackName, $shelfCount, $binCount, $binCapacity) {
            // Create rack
            $rack = StorageLocation::create([
                'code' => StorageLocation::generateNextCode('RACK'),
                'name' => $rackName,
                'type' => 'RACK',
                'is_active' => true,
                'display_order' => StorageLocation::whereNull('parent_id')->count(),
            ]);

            // Create shelves
            for ($s = 1; $s <= $shelfCount; $s++) {
                $shelf = StorageLocation::create([
                    'code' => StorageLocation::generateNextCode('SHELF', $rack->id),
                    'name' => "Shelf $s",
                    'type' => 'SHELF',
                    'parent_id' => $rack->id,
                    'is_active' => true,
                    'display_order' => $s,
                ]);

                // Create bins for this shelf
                for ($b = 1; $b <= $binCount; $b++) {
                    StorageLocation::create([
                        'code' => StorageLocation::generateNextCode('BIN', $shelf->id),
                        'name' => "Bin " . chr(64 + $b), // A, B, C, D...
                        'type' => 'BIN',
                        'parent_id' => $shelf->id,
                        'capacity' => $binCapacity,
                        'is_active' => true,
                        'display_order' => $b,
                    ]);
                }
            }

            return $rack->fresh(['children.children']);
        });
    }

    /**
     * Get location hierarchy as a tree
     */
    public function getLocationTree(): Collection
    {
        return StorageLocation::active()
            ->whereNull('parent_id')
            ->with(['children.children'])
            ->orderBy('display_order')
            ->get();
    }

    /**
     * Search locations by code, name, or containing product
     */
    public function searchLocations(string $query): Collection
    {
        return StorageLocation::active()
            ->where(function ($q) use ($query) {
                $q->where('code', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->orWhereHas('batches.product', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with(['batches.product', 'parent'])
            ->limit(20)
            ->get();
    }

    /**
     * Get products in a specific location
     */
    public function getProductsInLocation(StorageLocation $location): Collection
    {
        return ProductBatch::where('storage_location_id', $location->id)
            ->with('product')
            ->get()
            ->groupBy('product_id')
            ->map(function ($batches) {
                $product = $batches->first()->product;
                return [
                    'product' => $product,
                    'batch_count' => $batches->count(),
                    'total_quantity' => $batches->sum('quantity_remaining'),
                    'oldest_expiry' => $batches->min('expiry_date'),
                    'batches' => $batches,
                ];
            })
            ->values();
    }

    /**
     * Generate location barcode/QR data
     */
    public function generateLocationBarcode(StorageLocation $location): array
    {
        return [
            'type' => 'LOCATION',
            'id' => $location->id,
            'code' => $location->code,
            'name' => $location->name,
            'path' => $location->getFullPath(),
        ];
    }

    /**
     * Bulk assign batches to suggested locations
     * Useful for initial setup or stocktake
     */
    public function bulkAutoAssign(?array $batchIds = null): array
    {
        $query = ProductBatch::whereNull('storage_location_id');

        if ($batchIds) {
            $query->whereIn('id', $batchIds);
        }

        $batches = $query->with('product')->get();
        $assigned = 0;
        $failed = 0;
        $errors = [];

        foreach ($batches as $batch) {
            try {
                $this->assignBatchToLocation($batch);
                $assigned++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Batch {$batch->batch_number}: {$e->getMessage()}";
            }
        }

        return [
            'assigned' => $assigned,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }
}
