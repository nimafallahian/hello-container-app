<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Notifications\PaymentRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderPaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_flow_from_order_update_to_notification(): void
    {
        Notification::fake();
        
        Carbon::setTestNow(Carbon::parse('2024-01-09 10:00:00', 'Europe/Amsterdam'));
        
        $order = Order::factory()->create([
            'freight_payer_self' => true,
            'contract_number' => 'TEST-CONTRACT-001',
            'bl_number' => 'TEST-BL-001',
        ]);
        
        $order->update(['freight_payer_self' => false]);
        
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            PaymentRequest::class,
            function ($notification, $channels, $notifiable) use ($order) {
                return $notification->order->id === $order->id &&
                       $notification->order->contract_number === 'TEST-CONTRACT-001' &&
                       $notification->order->bl_number === 'TEST-BL-001' &&
                       $notifiable->routes['mail'] === 'test@example.com' &&
                       in_array('mail', $channels) &&
                       $notification->delay === null;
            }
        );
        
        Carbon::setTestNow();
    }
    
    public function test_complete_flow_with_office_hours_delay(): void
    {
        Notification::fake();
        
        Carbon::setTestNow(Carbon::parse('2024-01-13 15:00:00', 'Europe/Amsterdam'));
        
        $order = Order::factory()->create([
            'freight_payer_self' => true,
        ]);
        
        $order->update(['freight_payer_self' => false]);
        
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            PaymentRequest::class,
            function ($notification, $channels, $notifiable) use ($order) {
                $expectedDelay = 42 * 60 * 60;
                $actualDelay = $notification->delay ? Carbon::now()->diffInSeconds($notification->delay) : 0;
                
                return $notification->order->id === $order->id &&
                       $notifiable->routes['mail'] === 'test@example.com' &&
                       abs($actualDelay - $expectedDelay) < 5;
            }
        );
        
        Carbon::setTestNow();
    }
}
