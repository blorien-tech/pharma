<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false; // We only use created_at

    protected $fillable = [
        'batch_id',
        'from_location_id',
        'to_location_id',
        'quantity',
        'reason',
        'moved_by',
        'notes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the batch that was moved
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class);
    }

    /**
     * Get the location moved from
     */
    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'from_location_id');
    }

    /**
     * Get the location moved to
     */
    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'to_location_id');
    }

    /**
     * Get the user who moved the stock
     */
    public function movedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moved_by');
    }

    /**
     * Get the product (through batch)
     */
    public function product()
    {
        return $this->batch->product ?? null;
    }

    /**
     * Get formatted reason
     */
    public function getFormattedReason(): string
    {
        return match ($this->reason) {
            'RECEIPT' => 'Stock Receipt',
            'TRANSFER' => 'Location Transfer',
            'ADJUSTMENT' => 'Stock Adjustment',
            'SALE' => 'Sale',
            'RETURN' => 'Customer Return',
            'EXPIRED' => 'Expired Stock',
            'DAMAGED' => 'Damaged Goods',
            'QUARANTINE' => 'Quarantined',
            default => 'Other',
        };
    }

    /**
     * Check if this is an incoming movement
     */
    public function isIncoming(int $locationId): bool
    {
        return $this->to_location_id === $locationId;
    }

    /**
     * Check if this is an outgoing movement
     */
    public function isOutgoing(int $locationId): bool
    {
        return $this->from_location_id === $locationId;
    }

    /**
     * Scope: Get movements for a specific batch
     */
    public function scopeForBatch($query, int $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Scope: Get movements for a specific location
     */
    public function scopeForLocation($query, int $locationId)
    {
        return $query->where('from_location_id', $locationId)
            ->orWhere('to_location_id', $locationId);
    }

    /**
     * Scope: Get movements by reason
     */
    public function scopeByReason($query, string $reason)
    {
        return $query->where('reason', $reason);
    }

    /**
     * Scope: Get recent movements
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Create a stock movement record
     */
    public static function recordMovement(
        int $batchId,
        ?int $fromLocationId,
        int $toLocationId,
        int $quantity,
        string $reason,
        ?string $notes = null
    ): self {
        return self::create([
            'batch_id' => $batchId,
            'from_location_id' => $fromLocationId,
            'to_location_id' => $toLocationId,
            'quantity' => $quantity,
            'reason' => $reason,
            'moved_by' => auth()->id(),
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }
}
