<?php
namespace Database\Seeders;

use App\Models\Store;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'name' => 'SISIG REPUBLIC',
                'menu' => [
                    ['name' => 'Pork Sisig', 'price' => 65],
                    ['name' => 'Fried Chicken', 'price' => 55],
                    ['name' => 'Calamaris and Shrimp Tempura', 'price' => 75],
                    ['name' => 'Sisig with Rice', 'price' => 70],
                    ['name' => 'Proven', 'price' => 40],
                    ['name' => 'Corn Dog', 'price' => 35],
                ],
            ],
            [
                'name' => "TAG'Z FRUIT",
                'menu' => [
                    ['name' => 'Slice Mangga', 'price' => 25],
                    ['name' => 'Slice Pinya', 'price' => 25],
                    ['name' => 'Slice Bayabas', 'price' => 20],
                    ['name' => 'Slice Santol', 'price' => 20],
                ],
            ],
            [
                'name' => "ENJOY KIDDOS' HUB",
                'menu' => [
                    ['name' => 'Pastil', 'price' => 30],
                    ['name' => 'Fried Chicken', 'price' => 55],
                    ['name' => 'Chicken Curry', 'price' => 60],
                    ['name' => 'Humba', 'price' => 55],
                    ['name' => 'Hot Cake', 'price' => 25],
                ],
            ],
            [
                'name' => "ELMA'S TAKOYAKI & JAPANESE CAKE",
                'menu' => [
                    ['name' => 'Takoyaki', 'price' => 45],
                    ['name' => 'Japanese Cake', 'price' => 50],
                ],
            ],
            [
                'name' => 'DOUBLE G STREET FOODS',
                'menu' => [
                    ['name' => 'Kikiam', 'price' => 15],
                    ['name' => 'Fishball', 'price' => 15],
                ],
            ],
            [
                'name' => 'IL RUTTO',
                'menu' => [
                    ['name' => 'Soft Serve Ice Cream', 'price' => 40],
                    ['name' => 'Snacks', 'price' => 30],
                    ['name' => 'Burger and Fries', 'price' => 75],
                ],
            ],
            [
                'name' => 'SHAWARMA',
                'menu' => [
                    ['name' => 'Shawarma', 'price' => 50],
                ],
            ],
            [
                'name' => 'TUHOG CORNER',
                'menu' => [
                    ['name' => 'Fried Isaw', 'price' => 15],
                    ['name' => 'Baga', 'price' => 15],
                    ['name' => 'Hotdog', 'price' => 20],
                    ['name' => 'Siomai', 'price' => 20],
                    ['name' => 'Fried Shanghai', 'price' => 20],
                ],
            ],
            [
                'name' => 'JUCYMAE PURE/FRESH BUKO JUICE',
                'menu' => [
                    ['name' => 'Buko Juice', 'price' => 35],
                ],
            ],
            [
                'name' => 'FRESH FRUIT SALAD & SHAKE',
                'menu' => [
                    ['name' => 'Fruit Shake', 'price' => 45],
                ],
            ],
            [
                'name' => "JAC N' JELL FOOD HOUSE",
                'menu' => [
                    ['name' => 'Siomai Rice with Bagoong', 'price' => 55],
                    ['name' => 'Lemonade Juice', 'price' => 30],
                    ['name' => 'Sandwich Bread', 'price' => 25],
                ],
            ],
            [
                'name' => 'SWEET CORNER',
                'menu' => [
                    ['name' => 'Mais Con Yellow', 'price' => 35],
                    ['name' => 'Sweet Corn Pudding and Cakes', 'price' => 40],
                    ['name' => 'Sweet Corn Popcorn', 'price' => 30],
                    ['name' => 'Chao Fan SweetCorn Rice', 'price' => 45],
                ],
            ],
        ];

        foreach ($stores as $storeData) {
            $store = Store::create([
                'name' => $storeData['name'],
                'slug' => Str::slug($storeData['name']),
                'description' => 'Welcome to ' . $storeData['name'],
                'is_open' => true,
            ]);

            foreach ($storeData['menu'] as $item) {
                MenuItem::create([
                    'store_id' => $store->id,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'stock_count' => random_int(10, 50),
                    'expiration_date' => now()->addDays(random_int(3, 14))->toDateString(),
                    'is_available' => true,
                ]);
            }
        }
    }
}
