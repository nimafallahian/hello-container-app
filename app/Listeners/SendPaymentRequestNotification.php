<?php

namespace App\Listeners;

use App\Events\OrderPaymentRequired;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentRequestNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaymentRequired $event): void
    {
        // TODO: Implement notification logic
        // Access the order via $event->order
    }
}
