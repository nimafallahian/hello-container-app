<?php

namespace Tests\Feature;

use App\Events\OrderPaymentRequired;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderPaymentEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_payment_required_event_dispatched_when_freight_payer_self_changes_to_false(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        $order = Order::factory()->create([
            'freight_payer_self' => true,
        ]);
        
        $order->freight_payer_self = false;
        $order->save();
        
        Event::assertDispatched(OrderPaymentRequired::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }
    
    public function test_order_payment_required_event_not_dispatched_when_freight_payer_self_unchanged(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        $order = Order::factory()->create([
            'freight_payer_self' => false,
        ]);
        
        $order->update([
            'contract_number' => 'NEW-CONTRACT-123',
        ]);
        
        Event::assertNotDispatched(OrderPaymentRequired::class);
    }
    
    public function test_order_payment_required_event_not_dispatched_when_freight_payer_self_changes_to_true(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        $order = Order::factory()->create([
            'freight_payer_self' => false,
        ]);
        
        $order->update([
            'freight_payer_self' => true,
        ]);
        
        Event::assertNotDispatched(OrderPaymentRequired::class);
    }
    
    public function test_order_payment_required_event_not_dispatched_when_created_with_freight_payer_self_false(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        Order::factory()->create([
            'freight_payer_self' => false,
        ]);
        
        Event::assertNotDispatched(OrderPaymentRequired::class);
    }
}
