<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index()
    {
        $stores = Store::with([
            'menuItems:id,store_id,name,price,is_available,stock_count,expiration_date'
        ])->withCount([
            'menuItems as total_items',
            'menuItems as available_items' => function ($q) {
                $q->where('is_available', true)->where('stock_count', '>', 0);
            },
            'menuItems as unavailable_items' => function ($q) {
                $q->where('is_available', false)->orWhere('stock_count', '<=', 0);
            },
        ])->orderBy('name')->get();

        $overall = [
            'total_items' => MenuItem::count(),
            'available_items' => MenuItem::where('is_available', true)->where('stock_count', '>', 0)->count(),
            'unavailable_items' => MenuItem::where('is_available', false)->orWhere('stock_count', '<=', 0)->count(),
        ];

        $expiringItems = MenuItem::with('store:id,name')
            ->whereNotNull('expiration_date')
            ->orderBy('expiration_date', 'asc')
            ->get();

        return Inertia::render('Admin/Inventory/Index', [
            'stores' => $stores,
            'overall' => $overall,
            'expiringItems' => $expiringItems,
        ]);
    }

    public function update(Request $request, MenuItem $menu)
    {
        $data = $request->validate([
            'stock_count' => ['required', 'integer', 'min:0'],
            'expiration_date' => ['nullable', 'date'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $menu->update($data);

        return back()->with('success', 'Inventory updated.');
    }

    public function updateByRequest(Request $request)
    {
        $data = $request->validate([
            'menu_id' => ['required', 'integer', 'exists:menu_items,id'],
            'stock_count' => ['required', 'integer', 'min:0'],
            'expiration_date' => ['nullable', 'date'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $menu = MenuItem::findOrFail($data['menu_id']);

        $update = [
            'stock_count' => $data['stock_count'],
            'expiration_date' => $data['expiration_date'] ?? null,
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
            'items.*.stock_count' => ['required', 'integer', 'min:0'],
            'items.*.is_available' => ['required', 'boolean'],
            'items.*.expiration_date' => ['nullable', 'date'],
        ]);

        foreach ($data['items'] as $item) {
            MenuItem::where('id', $item['id'])->update([
                'stock_count' => $item['stock_count'],
                'is_available' => $item['is_available'],
                'expiration_date' => $item['expiration_date'],
            ]);
        }

        return back()->with('success', 'Bulk inventory update successful.');
    }
}
