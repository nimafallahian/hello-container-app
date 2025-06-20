<?php

namespace App\Listeners;

use App\Events\OrderPaymentRequired;
use App\Notifications\PaymentRequest;
use Illuminate\Support\Facades\Notification;

class SendPaymentRequestNotification
{
    public function __construct()
    {
        //
    }

    public function handle(OrderPaymentRequired $event): void
    {
        $delay = $this->calculateDelayForOfficeHours();

        $recipient = new \Illuminate\Notifications\AnonymousNotifiable;
        $recipient->route('mail', 'nima.fallahian@gmail.com');

        $notification = new PaymentRequest($event->order);

        if ($delay > 0) {
            $notification->delay(now()->addSeconds($delay));
        }

        Notification::send($recipient, $notification);
    }

    private function calculateDelayForOfficeHours(): int
    {
        $rotterdamTime = now()->setTimezone('Europe/Amsterdam');

        $hour = $rotterdamTime->hour;
        $dayOfWeek = $rotterdamTime->dayOfWeek;

        $isWeekday = $dayOfWeek >= 1 && $dayOfWeek <= 5;

        $isOfficeHours = $hour >= 9 && $hour < 17;

        if ($isWeekday && $isOfficeHours) {
            return 0;
        }

        $nextBusinessDay = $rotterdamTime->copy();

        if ($isWeekday && $hour >= 17) {
            $nextBusinessDay->addDay();
        }

        while ($nextBusinessDay->isWeekend()) {
            $nextBusinessDay->addDay();
        }

        $nextBusinessDay->setTime(9, 0, 0);

        if ($isWeekday && $hour < 9) {
            $nextBusinessDay = $rotterdamTime->copy()->setTime(9, 0, 0);
        }

        return (int) $rotterdamTime->diffInSeconds($nextBusinessDay);
    }
}
