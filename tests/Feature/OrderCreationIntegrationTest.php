<?php

namespace Tests\Feature;

use App\Events\OrderPaymentRequired;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderCreationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_order_with_freight_payer_other_triggers_payment_event(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        $orderData = [
            'contract_number' => 'INTEGRATION-TEST-001',
            'bl_number' => 'INTEGRATION-BL-001',
            'freight_payer_self' => '0',
        ];
        
        $response = $this->post(route('orders.store'), $orderData);
        
        $response->assertRedirect(route('orders.create'));
        $response->assertSessionHas('success');
        
        $order = Order::where('contract_number', 'INTEGRATION-TEST-001')->first();
        $this->assertNotNull($order);
        $this->assertFalse($order->freight_payer_self);
        
        Event::assertNotDispatched(OrderPaymentRequired::class);
    }
    
    public function test_updating_order_from_self_to_other_payer_triggers_event(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        $orderData = [
            'contract_number' => 'INTEGRATION-TEST-002',
            'bl_number' => 'INTEGRATION-BL-002',
            'freight_payer_self' => '1',
        ];
        
        $this->post(route('orders.store'), $orderData);
        
        $order = Order::where('contract_number', 'INTEGRATION-TEST-002')->first();
        $this->assertTrue($order->freight_payer_self);
        
        $order->update(['freight_payer_self' => false]);
        
        Event::assertDispatched(OrderPaymentRequired::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }
}
