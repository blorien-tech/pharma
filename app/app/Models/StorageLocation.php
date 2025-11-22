<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'capacity',
        'temperature_controlled',
        'temperature_min',
        'temperature_max',
        'display_order',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'temperature_controlled' => 'boolean',
            'temperature_min' => 'decimal:2',
            'temperature_max' => 'decimal:2',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the parent location (for hierarchical structure)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'parent_id');
    }

    /**
     * Get child locations
     */
    public function children(): HasMany
    {
        return $this->hasMany(StorageLocation::class, 'parent_id')->orderBy('display_order');
    }

    /**
     * Get all batches stored in this location
     */
    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class, 'storage_location_id');
    }

    /**
     * Get stock movements to this location
     */
    public function movementsIn(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'to_location_id');
    }

    /**
     * Get stock movements from this location
     */
    public function movementsOut(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'from_location_id');
    }

    /**
     * Get all stock movements (in and out)
     */
    public function movements()
    {
        return StockMovement::where('from_location_id', $this->id)
            ->orWhere('to_location_id', $this->id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get current occupancy count
     */
    public function getCurrentOccupancy(): int
    {
        return $this->batches()->whereHas('product', function ($query) {
            $query->where('is_active', true);
        })->count();
    }

    /**
     * Get remaining capacity
     */
    public function getRemainingCapacity(): ?int
    {
        if (!$this->capacity) {
            return null;
        }

        return $this->capacity - $this->getCurrentOccupancy();
    }

    /**
     * Check if location is full
     */
    public function isFull(): bool
    {
        if (!$this->capacity) {
            return false;
        }

        return $this->getCurrentOccupancy() >= $this->capacity;
    }

    /**
     * Get occupancy percentage
     */
    public function getOccupancyPercentage(): ?float
    {
        if (!$this->capacity) {
            return null;
        }

        return ($this->getCurrentOccupancy() / $this->capacity) * 100;
    }

    /**
     * Get full path (e.g., "Main Rack / Shelf 1 / Bin A")
     */
    public function getFullPath(): string
    {
        $path = [$this->name];
        $current = $this;

        while ($current->parent) {
            $current = $current->parent;
            array_unshift($path, $current->name);
        }

        return implode(' / ', $path);
    }

    /**
     * Get hierarchical level (0 = root, 1 = child, 2 = grandchild, etc.)
     */
    public function getLevel(): int
    {
        $level = 0;
        $current = $this;

        while ($current->parent) {
            $level++;
            $current = $current->parent;
        }

        return $level;
    }

    /**
     * Check if location requires temperature control
     */
    public function requiresTemperatureControl(): bool
    {
        return $this->temperature_controlled;
    }

    /**
     * Check if temperature is within range
     */
    public function isTemperatureValid(float $temperature): bool
    {
        if (!$this->temperature_controlled) {
            return true;
        }

        if ($this->temperature_min && $temperature < $this->temperature_min) {
            return false;
        }

        if ($this->temperature_max && $temperature > $this->temperature_max) {
            return false;
        }

        return true;
    }

    /**
     * Scope: Get only active locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get only root locations (no parent)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Get locations of specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Get available locations (not full)
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('capacity')
              ->orWhereRaw('capacity > (SELECT COUNT(*) FROM product_batches WHERE storage_location_id = storage_locations.id)');
        });
    }

    /**
     * Scope: Get temperature-controlled locations
     */
    public function scopeTemperatureControlled($query)
    {
        return $query->where('temperature_controlled', true);
    }

    /**
     * Generate next available code for a type
     */
    public static function generateNextCode(string $type, ?int $parentId = null): string
    {
        $prefix = match ($type) {
            'RACK' => 'R',
            'SHELF' => 'S',
            'BIN' => 'B',
            'FLOOR' => 'F',
            'REFRIGERATOR' => 'RF',
            'COUNTER' => 'C',
            'WAREHOUSE' => 'W',
            default => 'L',
        };

        // If has parent, append to parent's code
        if ($parentId) {
            $parent = self::find($parentId);
            if ($parent) {
                // Count siblings
                $siblingCount = self::where('parent_id', $parentId)->count();
                return $parent->code . '-' . $prefix . ($siblingCount + 1);
            }
        }

        // Root level - just use prefix and count
        $count = self::whereNull('parent_id')->where('type', $type)->count();
        return $prefix . ($count + 1);
    }

    /**
     * Generate name from code
     */
    public static function generateNameFromCode(string $code, string $type): string
    {
        $typeLabel = match ($type) {
            'RACK' => 'Rack',
            'SHELF' => 'Shelf',
            'BIN' => 'Bin',
            'FLOOR' => 'Floor',
            'REFRIGERATOR' => 'Refrigerator',
            'COUNTER' => 'Counter',
            'WAREHOUSE' => 'Warehouse',
            default => 'Location',
        };

        // Extract number from code (e.g., "R1" -> "1", "R1-S2" -> "2")
        $parts = explode('-', $code);
        $lastPart = end($parts);
        $number = preg_replace('/[^0-9]/', '', $lastPart);

        return $typeLabel . ' ' . $number;
    }
}
