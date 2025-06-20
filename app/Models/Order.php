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
    
    public static function validationRules(): array
    {
        return [
            'bl_release_date' => 'nullable|date',
            'bl_release_user_id' => 'nullable|integer|exists:users,id',
            'freight_payer_self' => 'required|boolean',
            'contract_number' => 'required|string|max:255',
            'bl_number' => 'required|string|max:255',
        ];
    }
    
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
