<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\MenuItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PosController extends Controller
{
    public function index()
    {
        $stores = Store::with(['menuItems' => fn ($q) => $q->where('is_available', true)])
            ->orderBy('name')
            ->get();

        $recentWalkIn = Order::with(['store', 'orderItems.menuItem'])
            ->where('order_type', 'walk_in')
            ->latest()
            ->take(15)
            ->get();

        $stats = [
            'total_sales_today' => Order::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'current_orders_count' => Order::whereIn('status', ['pending', 'confirmed', 'processing'])
                ->count(),
        ];

        return Inertia::render('Admin/Pos/Index', [
            'stores' => $stores,
            'recent_walk_in' => $recentWalkIn,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cod,gcash,paymaya',
        ]);

        $walkInUser = $this->getOrCreateWalkInUser();

        $order = DB::transaction(function () use ($request, $walkInUser) {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $menuItem = MenuItem::lockForUpdate()->findOrFail($item['menu_item_id']);
                
                if ($menuItem->stock_count < $item['quantity']) {
                    throw new \Exception("Insufficient stock for '{$menuItem->name}'. Only {$menuItem->stock_count} left.");
                }

                $subtotal = $menuItem->price * $item['quantity'];
                $totalAmount += $subtotal;
                $orderItemsData[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'price' => $menuItem->price,
                    'subtotal' => $subtotal,
                ];

                // Deduct stock for POS walk-in
                $menuItem->decrement('stock_count', $item['quantity']);
            }

            $order = Order::create([
                'user_id' => $walkInUser->id,
                'customer_name' => $request->customer_name ?: 'Walk-in Customer',
                'store_id' => $request->store_id,
                'order_number' => Order::generateOrderNumber(),
                'order_type' => 'walk_in',
                'status' => 'confirmed',
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'total_amount' => $totalAmount,
                'delivery_address' => 'Walk-in',
                'contact_phone' => 'N/A',
                'notes' => 'POS / Walk-in sale',
            ]);

            $order->orderItems()->createMany($orderItemsData);

            Payment::create([
                'order_id' => $order->id,
                'method' => $request->payment_method,
                'amount' => $totalAmount,
                'status' => 'verified',
            ]);

            return $order;
        });

        return redirect()->route('admin.pos.index')->with('flash', [
            'success' => 'Walk-in order ' . $order->order_number . ' created.',
            'order_id' => $order->id, // Pass order ID for receipt printing
        ]);
    }

    protected function getOrCreateWalkInUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'walkin@nemsu.edu.ph'],
            [
                'name' => 'Walk-in Customer',
                'password' => bcrypt(Str::random(32)),
                'role' => 'student',
            ]
        );
    }
}
