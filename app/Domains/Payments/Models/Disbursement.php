<?php

namespace App\Domains\Payments\Models;

use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Disbursement extends Model
{
    use LogsActivity;

    protected $fillable = [
        'order_id',
        'seller_id',
        'amount',
        'platform_fee_amount',
        'status',
        'bank_details',
        'requested_at',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee_amount' => 'decimal:2',
        'bank_details' => 'json',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount'])
            ->setDescriptionForEvent(fn(string $eventName) => "Disbursement has been {$eventName}");
    }

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Check if disbursement is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if disbursement is requested
     */
    public function isRequested(): bool
    {
        return $this->status === 'requested';
    }

    /**
     * Check if disbursement is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if disbursement is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmount(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    /**
     * Get formatted platform fee
     */
    public function getFormattedFee(): string
    {
        return '₱' . number_format($this->platform_fee_amount, 2);
    }

    /**
     * Get total amount (amount + fee deducted)
     */
    public function getNetAmount(): string
    {
        $net = $this->amount - $this->platform_fee_amount;
        return '₱' . number_format($net, 2);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'requested' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }
}
