<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoProgressOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(): void
    {
        $order = Order::find($this->orderId);
        if (!$order) { return; }

        // Simulate flow: confirmed -> preparing -> ready -> delivered
        $order->update(['status' => 'confirmed']);
        sleep(2);
        $order->update(['status' => 'preparing']);
        sleep(2);
        $order->update(['status' => 'ready']);
        sleep(2);
        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'payment_status' => $order->payment_method === 'cod' ? 'paid' : $order->payment_status,
        ]);
    }
}
