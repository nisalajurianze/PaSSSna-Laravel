<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Order Status Channel (Real-time updates for orders)
Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    return $user->can('view-order', $orderId);
});

// Reservation Channel (Real-time updates for reservations)
Broadcast::channel('reservation.{reservationId}', function ($user, $reservationId) {
    return $user->can('view-reservation', $reservationId);
});

// Admin Dashboard Channel (Real-time updates for admin dashboard)
Broadcast::channel('admin.dashboard', function ($user) {
    return $user->isAdmin();
});

// Customer Channel (Real-time updates for customer)
Broadcast::channel('customer.{customerId}', function ($user, $customerId) {
    return (int) $user->id === (int) $customerId;
});

// Kitchen Channel (Real-time updates for kitchen staff)
Broadcast::channel('kitchen.orders', function ($user) {
    return $user->hasRole(['chef', 'kitchen_staff']);
});

// Staff Notification Channel
Broadcast::channel('staff.notifications.{staffId}', function ($user, $staffId) {
    return (int) $user->id === (int) $staffId;
});

// Public order updates (for order status screens)
Broadcast::channel('public.order.{orderNumber}', function ($user, $orderNumber) {
    return true; // Public channel for order status displays
});

// Table status updates
Broadcast::channel('tables.status', function ($user) {
    return $user->isAdmin() || $user->hasRole('waiter');
});
