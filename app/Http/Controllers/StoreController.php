<?php
namespace App\Http\Controllers;

use App\Models\Store;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with(['menuItems'])
            ->withCount('orders')
            ->withAvg('ratings', 'rating')
            ->orderByDesc('is_open')
            ->orderBy('name')
            ->get();

        return Inertia::render('Stores/Index', [
            'stores' => $stores,
        ]);
    }

    public function show($slug)
    {
        $store = Store::where('slug', $slug)
            ->with(['menuItems']) // Load all items, frontend handles display logic
            ->with(['ratings.user'])
            ->firstOrFail();

        return Inertia::render('Stores/Show', [
            'store' => $store,
            'averageRating' => (float) ($store->ratings()->avg('rating') ?? 0),
            'totalRatings' => (int) $store->ratings()->count(),
        ]);
    }
}
