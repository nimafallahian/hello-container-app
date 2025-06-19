<?php

namespace App\Models;

use App\Events\OrderPaymentRequired;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    protected $casts = [
        'freight_payer_self' => 'boolean',
        'bl_release_date' => 'datetime',
    ];
    
    protected static function booted()
    {
        static::saved(function (Order $order) {
            if ($order->wasChanged('freight_payer_self') && 
                $order->freight_payer_self === false &&
                $order->getOriginal('freight_payer_self') === true) {
                OrderPaymentRequired::dispatch($order);
            }
        });
    }
}
