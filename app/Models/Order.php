<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'store_id', 'order_number', 'order_type',
        'status', 'payment_method', 'payment_status', 'total_amount',
        'delivery_address', 'contact_phone', 'notes', 'delivered_at'
    ];

    protected $casts = ['delivered_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function store() { return $this->belongsTo(Store::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function ratings() { return $this->hasMany(Rating::class); }

    /**
     * Mark the order as paid and trigger inventory deduction.
     */
    public function markAsPaid()
    {
        if ($this->payment_status === 'paid') {
            return;
        }

        \DB::transaction(function () {
            $this->loadMissing('orderItems.menuItem');

            // Re-verify stock before final deduction to be safe
            foreach ($this->orderItems as $item) {
                $menuItem = MenuItem::lockForUpdate()->find($item->menu_item_id);

                if (!$menuItem || $menuItem->stock_count < $item->quantity) {
                    throw new \Exception("Cannot finalize payment. '{$menuItem->name}' is out of stock.");
                }

                // Deduct stock (if not already deducted during reservation)
                // Note: In our current setup, we'll trigger the deduction here as requested.
                $menuItem->decrement('stock_count', $item->quantity);
            }

            $this->update(['payment_status' => 'paid']);
            $this->payment()->update(['status' => 'verified']);
        });
    }

    public static function generateOrderNumber()
    {
        return 'NCZ-' . strtoupper(uniqid());
    }
}
