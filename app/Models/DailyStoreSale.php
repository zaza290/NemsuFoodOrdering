<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStoreSale extends Model
{
    protected $fillable = ['store_id', 'revenue', 'date'];

    protected $casts = [
        'revenue' => 'decimal:2',
        'date' => 'date',
    ];

    public function store() { return $this->belongsTo(Store::class); }
}
