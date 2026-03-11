<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Store;
use App\Models\MenuItem;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Revenue by month (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $sum = Order::where('payment_status', 'paid')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_amount');
            $monthlyRevenue[] = [
                'label' => $month->format('M Y'),
                'amount' => $sum,
            ];
        }

        // Growth vs previous month
        $current = end($monthlyRevenue)['amount'] ?? 0;
        $prev = $monthlyRevenue[count($monthlyRevenue) - 2]['amount'] ?? 0;
        $growthPercent = $prev > 0 ? round((($current - $prev) / $prev) * 100, 2) : ($current > 0 ? 100 : 0);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
                'total_users' => User::whereIn('role', ['student', 'faculty'])->count(),
                'total_stores' => Store::count(),
                'delivered_today' => Order::whereDate('delivered_at', today())->count(),
                'income_growth_percent' => $growthPercent,
            ],
            'revenue_trend' => $monthlyRevenue,
            'recent_orders' => Order::with(['user', 'store'])
                ->latest()->take(10)->get(),
            'top_stores' => Store::withCount('orders')
                ->withAvg('ratings', 'rating')
                ->orderByDesc('orders_count')
                ->take(5)->get(),
        ]);
    }
}
