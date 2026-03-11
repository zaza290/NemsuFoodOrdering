<?php
use App\Http\Controllers\StoreController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\Admin;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public
Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

// Auth routes (from Breeze)
require __DIR__.'/auth.php';

// Unified Dashboard route for all authenticated users
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();
    if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('stores.index');
})->name('dashboard');

// Customer Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/{store}', [StoreController::class, 'show'])->name('stores.show');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/receipt', function (Order $order) {
        // TODO: Add authorization check to ensure only the user who owns the order or an admin can view it.
        $order->load(['user', 'store', 'orderItems.menuItem', 'payment']);
        return Inertia::render('Orders/Receipt', ['order' => $order]);
    })->name('orders.receipt');
    Route::patch('/orders/{order}/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('orders.confirm-delivery');

    Route::post('/orders/{order}/ratings', [RatingController::class, 'store'])->name('ratings.store');
});

// Admin Routes
Route::get('/admin/login', function () {
    return Inertia::render('Admin/Login');
})->name('admin.login');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pos', [Admin\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [Admin\PosController::class, 'store'])->name('pos.store');
    Route::get('/inventory', [Admin\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/bulk-update', [Admin\InventoryController::class, 'bulkUpdate'])->name('inventory.bulk-update');
    Route::patch('/inventory/menu-items/{menu}', [Admin\InventoryController::class, 'update'])->name('inventory.update');
    Route::patch('/inventory', [Admin\InventoryController::class, 'updateByRequest'])->name('inventory.update-fallback');

    // Store Management
    Route::resource('stores', Admin\StoreController::class);

    // Menu Management
    Route::resource('stores.menus', Admin\MenuController::class);

    // Order Management
    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/verify-payment', [Admin\OrderController::class, 'verifyPayment'])->name('orders.verify-payment');

    // User Management
    Route::resource('users', Admin\UserController::class)->except(['create', 'store']);
    Route::patch('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
});

// Admin Forbidden (friendly page)
Route::get('/admin/forbidden', function () {
    return Inertia::render('Admin/Forbidden');
})->name('admin.forbidden');
