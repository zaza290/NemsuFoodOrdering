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
                'image' => '/images/store1.jpg',
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
                'image' => '/images/store2.jpg',
                'menu' => [
                    ['name' => 'Slice Mangga', 'price' => 25],
                    ['name' => 'Slice Pinya', 'price' => 25],
                    ['name' => 'Slice Bayabas', 'price' => 20],
                    ['name' => 'Slice Santol', 'price' => 20],
                ],
            ],
            [
                'name' => "ENJOY KIDDOS' HUB",
                'image' => '/images/store3.jpg',
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
                'image' => '/images/store4.jpg',
                'menu' => [
                    ['name' => 'Takoyaki', 'price' => 45],
                    ['name' => 'Japanese Cake', 'price' => 50],
                ],
            ],
            [
                'name' => 'DOUBLE G STREET FOODS',
                'image' => '/images/store5.jpg',
                'menu' => [
                    ['name' => 'Kikiam', 'price' => 15],
                    ['name' => 'Fishball', 'price' => 15],
                ],
            ],
            [
                'name' => 'IL RUTTO',
                'image' => '/images/store6.jpg',
                'menu' => [
                    ['name' => 'Soft Serve Ice Cream', 'price' => 40],
                    ['name' => 'Snacks', 'price' => 30],
                    ['name' => 'Burger and Fries', 'price' => 75],
                ],
            ],
            [
                'name' => 'SHAWARMA',
                'image' => '/images/store7.jpg',
                'menu' => [
                    ['name' => 'Shawarma', 'price' => 50],
                ],
            ],
            [
                'name' => 'TUHOG CORNER',
                'image' => '/images/store8.jpg',
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
                'image' => '/images/store9.jpg',
                'menu' => [
                    ['name' => 'Buko Juice', 'price' => 35],
                ],
            ],
            [
                'name' => 'FRESH FRUIT SALAD & SHAKE',
                'image' => '/images/store10.jpg',
                'menu' => [
                    ['name' => 'Fruit Shake', 'price' => 45],
                ],
            ],
            [
                'name' => "JAC N' JELL FOOD HOUSE",
                'image' => '/images/store11.jpg',
                'menu' => [
                    ['name' => 'Siomai Rice with Bagoong', 'price' => 55],
                    ['name' => 'Lemonade Juice', 'price' => 30],
                    ['name' => 'Sandwich Bread', 'price' => 25],
                ],
            ],
            [
                'name' => 'SWEET CORNER',
                'image' => '/images/store12.jpg',
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
                'image' => $storeData['image'] ?? '/images/store' . rand(1, 12) . '.jpg',
                'is_open' => true,
            ]);

            foreach ($storeData['menu'] as $item) {
                MenuItem::create([
                    'store_id' => $store->id,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'current_stock' => random_int(10, 50),
                    'daily_target_stock' => 50,
                    'is_available' => true,
                ]);
            }
        }
    }
}
