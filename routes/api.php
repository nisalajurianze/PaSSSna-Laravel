<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ReservationController;
use App\Http\Controllers\Api\CustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API Routes (Rate Limited)
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Menu API (Public)
    Route::get('/menu', [MenuController::class, 'index']);
    Route::get('/menu/{id}', [MenuController::class, 'show'])->whereNumber('id');
    Route::get('/menu/category/{category}', [MenuController::class, 'byCategory']);
    Route::get('/categories', [MenuController::class, 'categories']);
    Route::get('/menu/categories', [MenuController::class, 'getCategories']);
    Route::get('/tables/available', [ReservationController::class, 'getAvailableTables']);
    Route::get('/reservation/check', [ReservationController::class, 'checkAvailability']);
    Route::get('/reservation/available-times', [ReservationController::class, 'getAvailableTimes']);
});

// Sanctum Protected Routes
Route::middleware('auth')->group(function () {
    // User Info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Profile (Rate Limited)
    Route::middleware(['throttle:60,1'])->group(function () {
        Route::get('/profile', [CustomerController::class, 'profile']);
        Route::put('/profile', [CustomerController::class, 'updateProfile']);
    });

    // Cart (Rate Limited)
    Route::middleware(['throttle:120,1'])->group(function () {
        Route::get('/cart', [OrderController::class, 'cart']);
        Route::post('/cart/add', [OrderController::class, 'addToCart']);
        Route::post('/cart/quick-add', [OrderController::class, 'quickAdd']);
        Route::put('/cart/update/{id}', [OrderController::class, 'updateCart'])->whereNumber('id');
        Route::delete('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->whereNumber('id');
        Route::post('/cart/clear', [OrderController::class, 'clearCart']);
    });

    // Orders (Rate Limited)
    Route::middleware(['throttle:60,1'])->group(function () {
        Route::post('/order', [OrderController::class, 'placeOrder']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}/details', [OrderController::class, 'details']);
        Route::get('/orders/{id}', [OrderController::class, 'show'])->whereNumber('id');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->whereNumber('id');
        Route::get('/order/status/{order}', [OrderController::class, 'getStatus']);
    });

    // Reservations (Rate Limited)
    Route::middleware(['throttle:60,1'])->group(function () {
        Route::post('/reservation', [ReservationController::class, 'store']);
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::get('/reservations/{id}', [ReservationController::class, 'show'])->whereNumber('id');
        Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel'])->whereNumber('id');
    });

    // Checkout (Rate Limited - Stricter for payment operations)
    Route::middleware(['throttle:30,1'])->group(function () {
        Route::post('/checkout', [OrderController::class, 'checkout']);
    });

    // Dining (Rate Limited)
    Route::middleware(['throttle:60,1'])->group(function () {
        Route::post('/dining/enter', [CustomerController::class, 'enterDining']);
        Route::post('/dining/exit', [CustomerController::class, 'exitDining']);
        Route::get('/dining/menu', [MenuController::class, 'diningMenu']);
        Route::post('/dining/custom-meal', [OrderController::class, 'createCustomMeal']);
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Real-time update polling endpoints (public but require auth check in controller)
Route::middleware(['throttle:120,1'])->group(function () {
    Route::get('/menu/check-updated', [MenuController::class, 'checkUpdated'])->name('api.menu.check-updated');
    Route::get('/orders/check-updated', [OrderController::class, 'checkUpdated'])->name('api.orders.check-updated');
    Route::get('/reservations/check-updated', [ReservationController::class, 'checkUpdated'])->name('api.reservations.check-updated');
});

// Admin API Routes (Rate Limited - Stricter for admin operations)
Route::middleware(['auth', 'admin.api', 'throttle:120,1'])->prefix('admin')->group(function () {
    // Dashboard Stats
    Route::get('/dashboard/stats', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'stats']);

    // Orders Management
    Route::get('/orders', [\App\Http\Controllers\Api\Admin\OrderController::class, 'index']);
    Route::put('/orders/{id}/status', [\App\Http\Controllers\Api\Admin\OrderController::class, 'updateStatus'])->whereNumber('id');

    // Reservations Management
    Route::get('/reservations', [\App\Http\Controllers\Api\Admin\ReservationController::class, 'index']);
    Route::put('/reservations/{id}/status', [\App\Http\Controllers\Api\Admin\ReservationController::class, 'updateStatus'])->whereNumber('id');

    // Inventory Management
    Route::get('/inventory', [\App\Http\Controllers\Api\Admin\InventoryController::class, 'index']);
    Route::post('/inventory/update', [\App\Http\Controllers\Api\Admin\InventoryController::class, 'update']);
});
