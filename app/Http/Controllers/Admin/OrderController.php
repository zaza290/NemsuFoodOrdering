<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Events\OrderStatusUpdated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'store', 'orderItems.menuItem', 'payment'])
            ->latest()->paginate(20);

        return Inertia::render('Admin/Orders/Index', ['orders' => $orders]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        if ($request->status === 'delivered') {
            $order->update(['delivered_at' => now()]);
        }

        // Send real-time notification
        broadcast(new OrderStatusUpdated($order));

        // Send database notification to user
        $order->user->notify(new \App\Notifications\OrderStatusNotification($order));

        return back()->with('success', 'Order status updated!');
    }

    public function verifyPayment(Request $request, Order $order)
    {
        try {
            $order->markAsPaid();

            // Send real-time notification
            broadcast(new OrderStatusUpdated($order));

            return back()->with('success', 'Payment verified and inventory updated!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
