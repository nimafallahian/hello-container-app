<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Request for Order #' . $this->order->id)
            ->line('A payment request has been generated for the following order:')
            ->line('Order ID: ' . $this->order->id)
            ->line('BL Release Date: ' . ($this->order->bl_release_date ? $this->order->bl_release_date->format('Y-m-d H:i:s') : 'Not set'))
            ->line('BL Release User ID: ' . ($this->order->bl_release_user_id ?? 'Not assigned'))
            ->line('Freight Payer Self: ' . ($this->order->freight_payer_self ? 'Yes' : 'No'))
            ->line('Contract Number: ' . $this->order->contract_number)
            ->line('BL Number: ' . $this->order->bl_number)
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Please process this payment request as soon as possible.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'contract_number' => $this->order->contract_number,
            'bl_number' => $this->order->bl_number,
        ];
    }
}
