<?php
namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceiptNotification extends Notification
{
    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $items = $this->order->orderItems()->with('menuItem')->get();
        $lines = [];
        foreach ($items as $it) {
            $lines[] = $it->menuItem?->name . ' ×' . $it->quantity . ' — ₱' . number_format((float) $it->subtotal, 2);
        }

        return (new MailMessage)
            ->subject('Payment Receipt: ' . $this->order->order_number)
            ->greeting('Hi ' . ($notifiable->name ?? 'Customer') . ',')
            ->line('Salamat! Nakapag-checkout ka na.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Payment Method: ' . strtoupper($this->order->payment_method))
            ->line('Payment Status: ' . strtoupper($this->order->payment_status))
            ->line('Total Amount: ₱' . number_format((float) $this->order->total_amount, 2))
            ->line('Items:')
            ->line(implode("\n", $lines))
            ->line('Delivery Address: ' . ($this->order->delivery_address ?? 'N/A'))
            ->line('Kung may katanungan, reply lang sa email na ito.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => 'receipt_sent',
            'message' => 'Payment receipt has been sent for order ' . $this->order->order_number,
        ];
    }
}
