<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index()
    {
        $stores = Store::with([
            'menuItems:id,store_id,name,price,is_available,current_stock,daily_target_stock'
        ])->withCount([
            'menuItems as total_items',
            'menuItems as available_items' => function ($q) {
                $q->where('is_available', true)->where('current_stock', '>', 0);
            },
            'menuItems as unavailable_items' => function ($q) {
                $q->where('is_available', false)->orWhere('current_stock', '<=', 0);
            },
        ])->orderBy('name')->get();

        $overall = [
            'total_items' => MenuItem::count(),
            'available_items' => MenuItem::where('is_available', true)->where('current_stock', '>', 0)->count(),
            'unavailable_items' => MenuItem::where('is_available', false)->orWhere('current_stock', '<=', 0)->count(),
        ];

        // We can replace expiring items with "Low Stock Items" for the new system
        $lowStockItems = MenuItem::with('store:id,name')
            ->whereColumn('current_stock', '<', DB::raw('daily_target_stock * 0.2')) // Less than 20% left
            ->orderBy('current_stock', 'asc')
            ->get();

        return Inertia::render('Admin/Inventory/Index', [
            'stores' => $stores,
            'overall' => $overall,
            'lowStockItems' => $lowStockItems,
        ]);
    }

    public function update(Request $request, MenuItem $menu)
    {
        $data = $request->validate([
            'current_stock' => ['required', 'integer', 'min:0'],
            'daily_target_stock' => ['required', 'integer', 'min:1'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $menu->update($data);

        return back()->with('success', 'Inventory updated.');
    }

    public function updateByRequest(Request $request)
    {
        $data = $request->validate([
            'menu_id' => ['required', 'integer', 'exists:menu_items,id'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'daily_target_stock' => ['required', 'integer', 'min:1'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $menu = MenuItem::findOrFail($data['menu_id']);

        $update = [
            'current_stock' => $data['current_stock'],
            'daily_target_stock' => $data['daily_target_stock'],
        ];

        if (array_key_exists('is_available', $data)) {
            $update['is_available'] = $data['is_available'];
        }

        $menu->update($update);

        return back()->with('success', 'Inventory updated.');
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.current_stock' => ['required', 'integer', 'min:0'],
            'items.*.daily_target_stock' => ['required', 'integer', 'min:1'],
            'items.*.is_available' => ['required', 'boolean'],
        ]);

        foreach ($data['items'] as $item) {
            MenuItem::where('id', $item['id'])->update([
                'current_stock' => $item['current_stock'],
                'daily_target_stock' => $item['daily_target_stock'],
                'is_available' => $item['is_available'],
            ]);
        }

        return back()->with('success', 'Bulk inventory update successful.');
    }
}
