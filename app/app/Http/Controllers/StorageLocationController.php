<?php

namespace App\Http\Controllers;

use App\Models\StorageLocation;
use App\Models\ProductBatch;
use App\Services\LocationService;
use Illuminate\Http\Request;

class StorageLocationController extends Controller
{
    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Display list of storage locations with hierarchy
     */
    public function index()
    {
        $locations = $this->locationService->getLocationTree();
        $stats = $this->getOverallStatistics();
        $alerts = $this->locationService->getLocationsNeedingAttention();

        return view('locations.index', compact('locations', 'stats', 'alerts'));
    }

    /**
     * Show the form for creating a new location
     */
    public function create()
    {
        $types = ['RACK', 'SHELF', 'BIN', 'FLOOR', 'REFRIGERATOR', 'COUNTER', 'WAREHOUSE'];
        $parentLocations = StorageLocation::active()
            ->whereIn('type', ['RACK', 'SHELF']) // Can only be parent of next level
            ->orderBy('code')
            ->get();

        return view('locations.create', compact('types', 'parentLocations'));
    }

    /**
     * Store a newly created location
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:RACK,SHELF,BIN,FLOOR,REFRIGERATOR,COUNTER,WAREHOUSE',
            'parent_id' => 'nullable|exists:storage_locations,id',
            'name' => 'nullable|string|max:100',
            'capacity' => 'nullable|integer|min:1',
            'temperature_controlled' => 'boolean',
            'temperature_min' => 'nullable|numeric',
            'temperature_max' => 'nullable|numeric|gte:temperature_min',
            'notes' => 'nullable|string',
        ]);

        // Auto-generate code if not provided
        $code = StorageLocation::generateNextCode(
            $validated['type'],
            $validated['parent_id'] ?? null
        );

        // Auto-generate name if not provided
        $name = $validated['name'] ?? StorageLocation::generateNameFromCode($code, $validated['type']);

        $location = StorageLocation::create([
            'code' => $code,
            'name' => $name,
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'capacity' => $validated['capacity'] ?? null,
            'temperature_controlled' => $validated['temperature_controlled'] ?? false,
            'temperature_min' => $validated['temperature_min'] ?? null,
            'temperature_max' => $validated['temperature_max'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'display_order' => StorageLocation::where('parent_id', $validated['parent_id'] ?? null)->count(),
            'is_active' => true,
        ]);

        return redirect()->route('locations.index')
            ->with('success', __('locations.location_created'));
    }

    /**
     * Quick create hierarchy
     */
    public function quickCreateHierarchy(Request $request)
    {
        $validated = $request->validate([
            'rack_name' => 'required|string|max:100',
            'shelf_count' => 'required|integer|min:1|max:20',
            'bin_count' => 'required|integer|min:1|max:20',
            'bin_capacity' => 'nullable|integer|min:1',
        ]);

        try {
            $rack = $this->locationService->createHierarchy(
                $validated['rack_name'],
                $validated['shelf_count'],
                $validated['bin_count'],
                $validated['bin_capacity'] ?? 10
            );

            $totalCreated = 1 + $validated['shelf_count'] + ($validated['shelf_count'] * $validated['bin_count']);

            return redirect()->route('locations.index')
                ->with('success', __('locations.hierarchy_created', ['count' => $totalCreated]));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified location with details
     */
    public function show(StorageLocation $location)
    {
        $location->load(['parent', 'children', 'batches.product']);
        $stats = $this->locationService->getLocationStatistics($location);
        $products = $this->locationService->getProductsInLocation($location);
        $recentMovements = $location->movements()->with(['batch.product', 'fromLocation', 'toLocation', 'movedBy'])->limit(20)->get();

        return view('locations.show', compact('location', 'stats', 'products', 'recentMovements'));
    }

    /**
     * Show the form for editing the location
     */
    public function edit(StorageLocation $location)
    {
        $types = ['RACK', 'SHELF', 'BIN', 'FLOOR', 'REFRIGERATOR', 'COUNTER', 'WAREHOUSE'];
        $parentLocations = StorageLocation::active()
            ->where('id', '!=', $location->id)
            ->whereIn('type', ['RACK', 'SHELF'])
            ->orderBy('code')
            ->get();

        return view('locations.edit', compact('location', 'types', 'parentLocations'));
    }

    /**
     * Update the specified location
     */
    public function update(Request $request, StorageLocation $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'capacity' => 'nullable|integer|min:1',
            'temperature_controlled' => 'boolean',
            'temperature_min' => 'nullable|numeric',
            'temperature_max' => 'nullable|numeric|gte:temperature_min',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $location->update($validated);

        return redirect()->route('locations.show', $location)
            ->with('success', __('locations.location_updated'));
    }

    /**
     * Remove the specified location
     */
    public function destroy(StorageLocation $location)
    {
        // Check if location has batches
        if ($location->batches()->exists()) {
            return back()->with('error', __('locations.cannot_delete_has_stock'));
        }

        // Check if location has children
        if ($location->children()->exists()) {
            return back()->with('error', __('locations.cannot_delete_has_children'));
        }

        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', __('locations.location_deleted'));
    }

    /**
     * API: Search locations for autocomplete
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $locations = $this->locationService->searchLocations($query);

        return response()->json($locations->map(function ($location) {
            return [
                'id' => $location->id,
                'code' => $location->code,
                'name' => $location->name,
                'full_path' => $location->getFullPath(),
                'type' => $location->type,
                'capacity' => $location->capacity,
                'occupancy' => $location->getCurrentOccupancy(),
                'is_full' => $location->isFull(),
                'available' => !$location->isFull(),
            ];
        }));
    }

    /**
     * API: Get suggested location for a product
     */
    public function suggestForProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        $suggestion = $this->locationService->suggestLocationForProduct($product);

        if (!$suggestion) {
            return response()->json([
                'suggested' => false,
                'message' => __('locations.no_available_location'),
            ]);
        }

        return response()->json([
            'suggested' => true,
            'location' => [
                'id' => $suggestion->id,
                'code' => $suggestion->code,
                'name' => $suggestion->name,
                'full_path' => $suggestion->getFullPath(),
                'capacity' => $suggestion->capacity,
                'occupancy' => $suggestion->getCurrentOccupancy(),
                'remaining' => $suggestion->getRemainingCapacity(),
            ],
            'reason' => $this->getSuggestionReason($product, $suggestion),
        ]);
    }

    /**
     * API: Assign batch to location
     */
    public function assignBatch(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:product_batches,id',
            'location_id' => 'nullable|exists:storage_locations,id',
            'notes' => 'nullable|string',
        ]);

        try {
            $batch = ProductBatch::findOrFail($request->batch_id);

            $updatedBatch = $this->locationService->assignBatchToLocation(
                $batch,
                $request->location_id,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => __('locations.batch_assigned'),
                'batch' => [
                    'id' => $updatedBatch->id,
                    'location_id' => $updatedBatch->storage_location_id,
                    'location_path' => $updatedBatch->getLocationPath(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * API: Move batch between locations
     */
    public function moveBatch(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:product_batches,id',
            'to_location_id' => 'required|exists:storage_locations,id',
            'reason' => 'nullable|in:TRANSFER,ADJUSTMENT,EXPIRED,DAMAGED,QUARANTINE,OTHER',
            'notes' => 'nullable|string',
        ]);

        try {
            $batch = ProductBatch::findOrFail($request->batch_id);

            $updatedBatch = $this->locationService->moveBatch(
                $batch,
                $request->to_location_id,
                $request->reason ?? 'TRANSFER',
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => __('locations.batch_moved'),
                'batch' => [
                    'id' => $updatedBatch->id,
                    'location_id' => $updatedBatch->storage_location_id,
                    'location_path' => $updatedBatch->getLocationPath(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk auto-assign unlocated batches
     */
    public function bulkAutoAssign()
    {
        try {
            $result = $this->locationService->bulkAutoAssign();

            $message = __('locations.bulk_assign_complete', [
                'assigned' => $result['assigned'],
                'failed' => $result['failed'],
            ]);

            if ($result['failed'] > 0) {
                return redirect()->route('locations.index')
                    ->with('warning', $message . ' ' . implode(', ', array_slice($result['errors'], 0, 3)));
            }

            return redirect()->route('locations.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get overall statistics
     */
    protected function getOverallStatistics(): array
    {
        $allLocations = StorageLocation::active()->get();
        $totalBatches = ProductBatch::whereNotNull('storage_location_id')->count();
        $unlocatedBatches = ProductBatch::whereNull('storage_location_id')->count();

        return [
            'total_locations' => $allLocations->count(),
            'total_bins' => $allLocations->where('type', 'BIN')->count(),
            'total_batches' => $totalBatches,
            'unlocated_batches' => $unlocatedBatches,
            'total_capacity' => $allLocations->sum('capacity'),
            'total_occupancy' => $totalBatches,
            'occupancy_percentage' => $allLocations->sum('capacity') > 0
                ? ($totalBatches / $allLocations->sum('capacity')) * 100
                : 0,
        ];
    }

    /**
     * Get reason for location suggestion
     */
    protected function getSuggestionReason($product, $location): string
    {
        $existingBatches = ProductBatch::where('product_id', $product->id)
            ->where('storage_location_id', $location->id)
            ->count();

        if ($existingBatches > 0) {
            return __('locations.suggestion_reason_existing', ['count' => $existingBatches]);
        }

        return __('locations.suggestion_reason_available');
    }
}
