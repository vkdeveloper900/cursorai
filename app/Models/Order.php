<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'package_id',
        'coupon_id',
        'original_price',
        'discount_amount',
        'final_price',
        'gateway_name',
        'gateway_order_id',
        'status',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {

            if (!$order->order_number) {

                $year = Carbon::now()->year;

                // Count orders for current year
                $count = self::whereYear('created_at', $year)->count() + 1;

                $order->order_number = $count . '/' . $year;
            }
        });
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}


