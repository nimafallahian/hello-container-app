<?php

namespace Tests\Unit;

use App\Events\OrderPaymentRequired;
use App\Listeners\SendPaymentRequestNotification;
use App\Models\Order;
use App\Notifications\PaymentRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendPaymentRequestNotificationTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_notification_sent_immediately_during_office_hours(): void
    {
        Notification::fake();
        
        Carbon::setTestNow(Carbon::parse('2024-01-09 10:00:00', 'Europe/Amsterdam'));
        
        $order = Order::factory()->create();
        $event = new OrderPaymentRequired($order);
        
        $listener = new SendPaymentRequestNotification();
        $listener->handle($event);
        
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            PaymentRequest::class,
            function ($notification, $channels, $notifiable) use ($order) {
                return $notification->order->id === $order->id &&
                       $notifiable->routes['mail'] === 'nima.fallahian@gmail.com' &&
                       $notification->delay === null;
            }
        );
        
        Carbon::setTestNow();
    }
    
    public function test_notification_delayed_when_sent_on_sunday(): void
    {
        Notification::fake();
        
        Carbon::setTestNow(Carbon::parse('2024-01-07 14:00:00', 'Europe/Amsterdam'));
        
        $order = Order::factory()->create();
        $event = new OrderPaymentRequired($order);
        
        $listener = new SendPaymentRequestNotification();
        $listener->handle($event);
        
        $expectedDelay = 19 * 60 * 60;
        
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            PaymentRequest::class,
            function ($notification, $channels, $notifiable) use ($order, $expectedDelay) {
                $actualDelay = $notification->delay ? Carbon::now()->diffInSeconds($notification->delay) : 0;
                return $notification->order->id === $order->id &&
                       $notifiable->routes['mail'] === 'nima.fallahian@gmail.com' &&
                       abs($actualDelay - $expectedDelay) < 5;
            }
        );
        
        Carbon::setTestNow();
    }
    
    public function test_notification_delayed_when_sent_after_office_hours(): void
    {
        Notification::fake();
        
        Carbon::setTestNow(Carbon::parse('2024-01-10 18:00:00', 'Europe/Amsterdam'));
        
        $order = Order::factory()->create();
        $event = new OrderPaymentRequired($order);
        
        $listener = new SendPaymentRequestNotification();
        $listener->handle($event);
        
        $expectedDelay = 15 * 60 * 60;
        
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            PaymentRequest::class,
            function ($notification, $channels, $notifiable) use ($order, $expectedDelay) {
                $actualDelay = $notification->delay ? Carbon::now()->diffInSeconds($notification->delay) : 0;
                return $notification->order->id === $order->id &&
                       $notifiable->routes['mail'] === 'nima.fallahian@gmail.com' &&
                       abs($actualDelay - $expectedDelay) < 5;
            }
        );
        
        Carbon::setTestNow();
    }
    
    public function test_notification_delayed_when_sent_before_office_hours(): void
    {
        Notification::fake();
        
        Carbon::setTestNow(Carbon::parse('2024-01-08 07:00:00', 'Europe/Amsterdam'));
        
        $order = Order::factory()->create();
        $event = new OrderPaymentRequired($order);
        
        $listener = new SendPaymentRequestNotification();
        $listener->handle($event);
        
        $expectedDelay = 2 * 60 * 60;
        
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            PaymentRequest::class,
            function ($notification, $channels, $notifiable) use ($order, $expectedDelay) {
                $actualDelay = $notification->delay ? Carbon::now()->diffInSeconds($notification->delay) : 0;
                return $notification->order->id === $order->id &&
                       $notifiable->routes['mail'] === 'nima.fallahian@gmail.com' &&
                       abs($actualDelay - $expectedDelay) < 5;
            }
        );
        
        Carbon::setTestNow();
    }
    
    public function test_notification_delayed_when_sent_on_friday_evening(): void
    {
        Notification::fake();
        
        Carbon::setTestNow(Carbon::parse('2024-01-12 20:00:00', 'Europe/Amsterdam'));
        
        $order = Order::factory()->create();
        $event = new OrderPaymentRequired($order);
        
        $listener = new SendPaymentRequestNotification();
        $listener->handle($event);
        
        $expectedDelay = 61 * 60 * 60;
        
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            PaymentRequest::class,
            function ($notification, $channels, $notifiable) use ($order, $expectedDelay) {
                $actualDelay = $notification->delay ? Carbon::now()->diffInSeconds($notification->delay) : 0;
                return $notification->order->id === $order->id &&
                       $notifiable->routes['mail'] === 'nima.fallahian@gmail.com' &&
                       abs($actualDelay - $expectedDelay) < 5;
            }
        );
        
        Carbon::setTestNow();
    }
}
