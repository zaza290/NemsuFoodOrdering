<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use App\Models\Payment;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\AutoProgressOrder;
use App\Notifications\PaymentReceiptNotification;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['store', 'orderItems.menuItem', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('Orders/Index', ['orders' => $orders]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cod,gcash,paymaya',
            'contact_phone' => 'required|string',
            'account_number' => 'required_if:payment_method,gcash,paymaya|nullable|string',
            'reference_number' => 'nullable|string',
            'proof_image' => 'nullable|image|max:2048',
            'delivery_address' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalAmount = 0;
                $orderItemsData = [];

                foreach ($request->items as $item) {
                    // Lock the row for update to handle concurrent requests safely
                    $menuItem = MenuItem::lockForUpdate()->findOrFail($item['menu_item_id']);

                    // Double-check availability and stock after locking
                    if (!$menuItem->isPurchasable()) {
                        throw new \Exception("The item '{$menuItem->name}' is no longer available or is out of stock.");
                    }

                    if ($menuItem->current_stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for '{$menuItem->name}'. Only {$menuItem->current_stock} left.");
                    }

                    // 4. Calculate subtotal and track totals
                    $subtotal = $menuItem->price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $orderItemsData[] = [
                        'menu_item_id' => $menuItem->id,
                        'quantity' => $item['quantity'],
                        'price' => $menuItem->price,
                        'subtotal' => $subtotal,
                    ];

                    // Note: In our current setup, we'll trigger the deduction only when marked as 'Paid'
                    // as requested. We just check availability here.
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'store_id' => $request->store_id,
                    'order_number' => Order::generateOrderNumber(),
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'unpaid',
                    'total_amount' => $totalAmount,
                    'contact_phone' => $request->contact_phone,
                    'delivery_address' => $request->delivery_address,
                    'notes' => $request->notes,
                ]);

                $order->orderItems()->createMany($orderItemsData);

                $paymentData = [
                    'order_id' => $order->id,
                    'method' => $request->payment_method,
                    'account_number' => $request->account_number,
                    'amount' => $totalAmount,
                    'status' => 'pending',
                ];
                if ($request->has('reference_number')) {
                    $paymentData['reference_number'] = $request->reference_number;
                }
                if ($request->hasFile('proof_image')) {
                    $path = $request->file('proof_image')->store('payments', 'public');
                    $paymentData['proof_image'] = $path;
                }
                Payment::create($paymentData);

                // Broadcast the new order event to the admin channel
                broadcast(new OrderPlaced($order));

                // Real-time notification to Admin
                broadcast(new OrderStatusUpdated($order));

                if (in_array($order->payment_method, ['gcash', 'paymaya'])) {
                    // $order->user->notify(new PaymentReceiptNotification($order));
                }

                // Real-time notification would go here
                // broadcast(new OrderStatusUpdated($order));
            });

            return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function confirmDelivery(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($order) {
                $order->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);

                // For COD, marking as delivered means it's paid and stock should be deducted
                if ($order->payment_method === 'cod') {
                    $order->markAsPaid();
                }

                // Send real-time notification to Admin
                broadcast(new OrderStatusUpdated($order));
            });

            return back()->with('success', 'Order confirmed as delivered!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $order->load(['store', 'orderItems.menuItem', 'payment', 'ratings']);

        return Inertia::render('Orders/Show', ['order' => $order]);
    }
}
