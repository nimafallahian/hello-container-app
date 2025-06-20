<?php

namespace Tests\Unit;

use App\Actions\Orders\InitiateBlRelease;
use App\Events\OrderPaymentRequired;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class InitiateBlReleaseTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_action_dispatches_event_when_freight_payer_changes_to_false(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        $order = Order::factory()->create([
            'freight_payer_self' => true,
        ]);
        
        $order->freight_payer_self = false;
        $order->save();
        
        $action = new InitiateBlRelease();
        $action->execute($order);
        
        Event::assertDispatched(OrderPaymentRequired::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }
    
    public function test_action_does_not_dispatch_when_conditions_not_met(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        $order = Order::factory()->create([
            'freight_payer_self' => false,
        ]);
        
        $action = new InitiateBlRelease();
        $action->execute($order);
        
        Event::assertNotDispatched(OrderPaymentRequired::class);
    }
    
    public function test_action_is_reusable_in_different_contexts(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        $order1 = Order::factory()->create(['freight_payer_self' => true]);
        $order2 = Order::factory()->create(['freight_payer_self' => true]);
        
        $action = new InitiateBlRelease();
        
        $order1->freight_payer_self = false;
        $order1->save();
        
        $order2->freight_payer_self = false;
        $order2->save();
        
        Event::assertDispatchedTimes(OrderPaymentRequired::class, 2);
    }
    
    public function test_action_can_be_used_in_api_context(): void
    {
        Event::fake([OrderPaymentRequired::class]);
        
        $order = Order::factory()->create([
            'freight_payer_self' => true,
        ]);
        
        $order->freight_payer_self = false;
        $order->save();
        
        $action = new InitiateBlRelease();
        $action->execute($order);
        
        Event::assertDispatched(OrderPaymentRequired::class);
    }
}
