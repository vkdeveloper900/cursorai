<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'max_discount',
        'min_order_amount',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_to',
        'status',
        'description',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Remaining usage helper
    public function getRemainingUsageAttribute()
    {
        if (is_null($this->usage_limit)) {
            return null;
        }

        return max(0, $this->usage_limit - $this->used_count);
    }

    public function isExpired()
    {
        return now()->greaterThan($this->valid_to);
    }

    public function isValid()
    {
        return $this->status === 'active'
            && now()->between($this->valid_from, $this->valid_to)
            && (
                is_null($this->usage_limit)
                || $this->used_count < $this->usage_limit
            );
    }
}
