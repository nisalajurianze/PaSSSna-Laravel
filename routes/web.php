<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\ReservationController as CustomerReservationController;
use App\Http\Controllers\Customer\ContactController as CustomerContactController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\LoyaltyController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DiningController as AdminDiningController;
use App\Http\Controllers\Dining\DiningController as DiningController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-conditions', [HomeController::class, 'termsConditions'])->name('terms.conditions');

// Menu Routes (Public)
Route::get('/menu', [CustomerMenuController::class, 'index'])->name('menu');
Route::get('/menu/search', [CustomerMenuController::class, 'search'])->name('menu.search');
Route::get('/menu/{category}', [CustomerMenuController::class, 'byCategory'])->name('menu.category');
Route::get('/menu/item/{id}', [CustomerMenuController::class, 'show'])->name('menu.show');

// Reservation Routes (Require Login)
Route::middleware('auth')->group(function () {
    Route::get('/reservation', [CustomerReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservation', [CustomerReservationController::class, 'store'])->name('reservation.store');
    Route::get('/reservation/success/{id}', [CustomerReservationController::class, 'success'])->name('reservation.success');
});

// Contact Routes
Route::get('/contact', [CustomerContactController::class, 'index'])->name('contact');
Route::post('/contact', [CustomerContactController::class, 'store'])->name('contact.store');
Route::get('/faq', [CustomerContactController::class, 'faq'])->name('faq');
Route::post('/review', [ContactController::class, 'submitReview'])->name('review.store')->middleware('auth');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Registration Routes
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Cart Routes (Session-based - No authentication required)
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/update-quantity/{id}', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove.post');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/apply-promo', [CartController::class, 'applyPromo'])->name('cart.applyPromo');
Route::post('/cart/remove-promo', [CartController::class, 'removePromo'])->name('cart.removePromo');
Route::get('/cart/summary', [CartController::class, 'getSummary'])->name('cart.getSummary');
Route::post('/checkout/direct', [CartController::class, 'directCheckout'])->name('checkout.direct');

// Checkout Routes (Require Authentication)
Route::middleware(['auth', 'customer'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/apply-promo', [CheckoutController::class, 'applyPromo'])->name('checkout.apply-promo');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Dining Section (Table Login - No authentication required)
Route::prefix('dining')->name('dining.')->group(function () {
    Route::get('/', [DiningController::class, 'loginForm'])->name('login');
    Route::post('/login', [DiningController::class, 'login'])->name('login.submit');

    Route::middleware('dining.session')->group(function () {
        Route::get('/menu', [DiningController::class, 'menu'])->name('menu');
        Route::post('/cart/add', [DiningController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/update/{key}', [DiningController::class, 'updateCart'])->name('cart.update');
        Route::post('/cart/remove/{key}', [DiningController::class, 'removeCartItem'])->name('cart.remove');
        Route::post('/cart/clear', [DiningController::class, 'clearCart'])->name('cart.clear');
        Route::get('/custom-meal', [DiningController::class, 'customMealForm'])->name('custom');
        Route::post('/custom-meal', [DiningController::class, 'addCustomMeal'])->name('custom.add');
        Route::post('/place-order', [DiningController::class, 'placeOrder'])->name('order.place');
        Route::post('/close', [DiningController::class, 'closeSession'])->name('close');
        Route::get('/updates', [DiningController::class, 'updates'])->name('updates');
    });
});

// Customer Panel Routes
Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware(['auth', 'customer'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

        // Orders
        Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/orders/{order}/invoice', [CustomerOrderController::class, 'invoice'])->name('orders.invoice');

        // Reservations
        Route::get('/reservations', [CustomerReservationController::class, 'index'])->name('reservations');
        Route::get('/reservations/{reservation}', [CustomerReservationController::class, 'show'])->name('reservations.show');
        Route::post('/reservations/{reservation}/cancel', [CustomerReservationController::class, 'cancel'])->name('reservations.cancel');

        // Profile
        Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [CustomerDashboardController::class, 'updatePassword'])->name('profile.password');
        Route::delete('/profile', [CustomerDashboardController::class, 'deleteAccount'])->name('profile.delete');

        // Loyalty & Rewards
        Route::get('/loyalty', [LoyaltyController::class, 'index'])->name('loyalty');
        Route::post('/loyalty/redeem', [LoyaltyController::class, 'redeem'])->name('loyalty.redeem');
        Route::post('/loyalty/{id}/use', [LoyaltyController::class, 'useReward'])->name('loyalty.use');

        // Notifications
        Route::post('/notifications/read', [CustomerDashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [CustomerDashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');
        Route::get('/notifications/check', [CustomerDashboardController::class, 'checkNewNotifications'])->name('notifications.check');
        Route::get('/notifications/recent', [CustomerDashboardController::class, 'getRecentNotifications'])->name('notifications.recent');

        // Spending
        Route::get('/total-spent', [CustomerDashboardController::class, 'totalSpent'])->name('total-spent');
    });
});

// PDF Generation Routes
Route::get('/pdf/order/{order}', [PdfController::class, 'order'])->name('pdf.order');
Route::get('/pdf/invoice/{order}', [PdfController::class, 'invoice'])->name('pdf.invoice');
Route::get('/pdf/report/{type}', [PdfController::class, 'report'])->name('pdf.report');

// Admin Panel Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dining
    Route::get('/dining', [AdminDiningController::class, 'index'])->name('dining.index');
    Route::post('/dining/{session}/close', [AdminDiningController::class, 'close'])->name('dining.close');

    // Orders
    Route::resource('orders', OrderController::class);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/type/{type}', [OrderController::class, 'byType'])->name('orders.byType');
    Route::post('/orders/manual', [OrderController::class, 'storeManual'])->name('orders.storeManual');

    // Reservations
    Route::get('/reservations/calendar', [ReservationController::class, 'calendar'])->name('reservations.calendar');
    Route::resource('reservations', ReservationController::class);
    Route::patch('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
    Route::post('/reservations/manual', [ReservationController::class, 'storeManual'])->name('reservations.storeManual');

    // Menu
    Route::resource('menu', MenuController::class);
    Route::patch('/menu/{menu}/toggle-availability', [MenuController::class, 'toggleAvailability'])->name('menu.toggleAvailability');

    // Inventory
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.lowStock');
    Route::resource('inventory', InventoryController::class)->parameters(['inventory' => 'item']);
    Route::post('/inventory/{item}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');

    // Tables
    Route::resource('tables', TableController::class);
    Route::patch('/tables/{table}/toggle', [TableController::class, 'toggleStatus'])->name('tables.toggle');
    Route::get('/tables/floor-plan', [TableController::class, 'floorPlan'])->name('tables.floorPlan');
    Route::get('/tables/availability', [TableController::class, 'availability'])->name('tables.availability');

    // Staff
    Route::resource('staff', StaffController::class);
    Route::patch('/staff/{staff}/toggle', [StaffController::class, 'toggleStatus'])->name('staff.toggle');
    Route::get('/staff/schedule', [StaffController::class, 'schedule'])->name('staff.schedule');
    Route::post('/staff/schedule/update', [StaffController::class, 'updateSchedule'])->name('staff.schedule.update');

    // Promotions
    Route::resource('promotions', PromotionController::class);
    Route::patch('/promotions/{promotion}/toggle', [PromotionController::class, 'toggle'])->name('promotions.toggle');
    Route::get('/promotions/{promotion}/analytics', [PromotionController::class, 'analytics'])->name('promotions.analytics');

    // Contact Messages
    Route::resource('contact', ContactController::class);
    Route::post('/contact/{contact}/reply', [ContactController::class, 'reply'])->name('contact.reply');
    Route::get('/contact/mark-all-read', [ContactController::class, 'markAllAsRead'])->name('contact.markAllAsRead');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventoryReport'])->name('reports.inventory');
    Route::get('/reports/staff', [ReportController::class, 'staffReport'])->name('reports.staff');
});

// Fallback Route
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
