<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'store_id', 'name', 'description', 'price', 'image', 
        'daily_target_stock', 'current_stock', 'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'daily_target_stock' => 'integer',
        'current_stock' => 'integer',
    ];

    public function store() { return $this->belongsTo(Store::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function ratings() { return $this->hasMany(Rating::class); }
    public function wasteLogs() { return $this->hasMany(WasteLog::class); }

    /**
     * Scope a query to only include purchasable items.
     * Purchasable = Available and In-Stock.
     */
    public function scopePurchasable($query)
    {
        return $query->where('is_available', true)
                     ->where('current_stock', '>', 0);
    }

    /**
     * Check if the item is in stock.
     */
    public function inStock(): bool
    {
        return $this->current_stock > 0;
    }

    /**
     * Check if the item is truly purchasable.
     */
    public function isPurchasable(): bool
    {
        return $this->is_available && $this->inStock();
    }
}
