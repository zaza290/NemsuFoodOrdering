<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index(Store $store)
    {
        $menuItems = $store->menuItems()->orderBy('name')->get();

        return Inertia::render('Admin/Menus/Index', [
            'store' => $store,
            'menuItems' => $menuItems,
        ]);
    }

    public function store(Request $request, Store $store)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_count' => ['nullable', 'integer', 'min:0'],
            'expiration_date' => ['nullable', 'date'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $data['store_id'] = $store->id;
        $data['is_available'] = $data['is_available'] ?? true;

        MenuItem::create($data);

        return redirect()
            ->route('admin.stores.menus.index', $store)
            ->with('success', 'Menu item created successfully.');
    }

    public function update(Request $request, Store $store, MenuItem $menu)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock_count' => ['sometimes', 'integer', 'min:0'],
            'expiration_date' => ['sometimes', 'nullable', 'date'],
            'is_available' => ['sometimes', 'boolean'],
        ]);

        $menu->update($data);

        return redirect()->back()->with('success', 'Menu item updated successfully.');
    }

    public function destroy(Store $store, MenuItem $menu)
    {
        $menu->delete();

        return redirect()
            ->route('admin.stores.menus.index', $store)
            ->with('success', 'Menu item deleted successfully.');
    }
}
