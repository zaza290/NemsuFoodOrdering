<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteLog extends Model
{
    protected $fillable = ['store_id', 'menu_item_id', 'quantity_wasted', 'lost_profit', 'date'];

    protected $casts = [
        'lost_profit' => 'decimal:2',
        'date' => 'date',
        'quantity_wasted' => 'integer',
    ];

    public function store() { return $this->belongsTo(Store::class); }
    public function menuItem() { return $this->belongsTo(MenuItem::class); }
}
