<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'image', 'is_open'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function menuItems() { return $this->hasMany(MenuItem::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function ratings() { return $this->hasMany(Rating::class); }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }
}
