<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with('menuItems')
            ->withCount('orders')
            ->withAvg('ratings', 'rating')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Stores/Index', [
            'stores' => $stores,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_open' => ['required', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        Store::create($data);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Store created successfully.');
    }

    public function update(Request $request, Store $store)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_open' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('name', $data)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $store->update($data);

        return redirect()->back()->with('success', 'Store updated successfully.');
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return redirect()->back()->with('success', 'Store deleted successfully.');
    }
}
