<?php

namespace App\Actions\Orders;

use App\Events\OrderPaymentRequired;
use App\Models\Order;

class InitiateBlRelease
{
    public function execute(Order $order): void
    {
        if ($order->wasChanged('freight_payer_self') && 
            $order->freight_payer_self === false &&
            $order->getOriginal('freight_payer_self') === true) {
            OrderPaymentRequired::dispatch($order);
        }
    }
} 