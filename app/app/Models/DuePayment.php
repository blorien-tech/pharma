<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'due_id',
        'user_id',
        'amount',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the due this payment belongs to
     */
    public function due()
    {
        return $this->belongsTo(Due::class);
    }

    /**
     * Get the user who recorded this payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
