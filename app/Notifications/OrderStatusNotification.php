<?php
namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class OrderStatusNotification extends Notification
{
    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $messages = [
            'confirmed' => 'Your order ' . $this->order->order_number . ' has been confirmed!',
            'preparing' => 'Your order is now being prepared!',
            'ready' => 'Your order is ready for pickup/delivery!',
            'delivered' => 'Your order has been delivered successfully!',
            'cancelled' => 'Your order has been cancelled.',
        ];

        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'message' => $messages[$this->order->status] ?? 'Order status updated.',
        ];
    }
}
