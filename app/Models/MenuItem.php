<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['store_id', 'name', 'description', 'price', 'image', 'stock_count', 'expiration_date', 'is_available'];

    protected $casts = [
        'expiration_date' => 'date',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function store() { return $this->belongsTo(Store::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function ratings() { return $this->hasMany(Rating::class); }

    /**
     * Scope a query to only include purchasable items.
     * Purchasable = Available, In-Stock, and Not Expired.
     */
    public function scopePurchasable($query)
    {
        return $query->where('is_available', true)
                     ->where('stock_count', '>', 0)
                     ->where(function ($q) {
                         $q->whereNull('expiration_date')
                           ->orWhere('expiration_date', '>=', now()->toDateString());
                     });
    }

    /**
     * Check if the item is in stock.
     */
    public function inStock(): bool
    {
        return $this->stock_count > 0;
    }

    /**
     * Check if the item is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expiration_date) return false;
        return $this->expiration_date->isPast();
    }

    /**
     * Check if the item is truly purchasable.
     */
    public function isPurchasable(): bool
    {
        return $this->is_available && $this->inStock() && !$this->isExpired();
    }
}
