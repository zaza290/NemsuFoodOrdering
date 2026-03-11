<?php
namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'delivered') {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'menu_item_id' => 'nullable|exists:menu_items,id',
        ]);

        Rating::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'menu_item_id' => $request->menu_item_id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', 'Rating submitted!');
    }
}
