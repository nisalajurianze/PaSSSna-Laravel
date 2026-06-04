<?php

return [
    // Restaurant Information
    'name' => env('RESTAURANT_NAME', 'PaSSSna Restaurant'),
    'tagline' => 'Taste the Passion',
    'contact_email' => env('RESTAURANT_EMAIL', 'info@passsna.com'),
    'contact_phone' => env('RESTAURANT_PHONE', '+1 (555) 123-4567'),
    'address' => env('RESTAURANT_ADDRESS', '123 Gourmet Street, Food City'),

    // Admin / Security
    'admin_password' => env('ADMIN_PASSWORD', 'PaSSSna_log'),

    'dining_kiosk' => [
        'table_number' => env('DINING_KIOSK_TABLE', null),
    ],

    // Operating Hours
    'opening_hours' => [
        0 => ['open' => 12, 'close' => 21], // Sunday
        1 => ['open' => 11, 'close' => 22], // Monday
        2 => ['open' => 11, 'close' => 22], // Tuesday
        3 => ['open' => 11, 'close' => 22], // Wednesday
        4 => ['open' => 11, 'close' => 22], // Thursday
        5 => ['open' => 11, 'close' => 23], // Friday
        6 => ['open' => 11, 'close' => 23], // Saturday
    ],

    // Reservation Settings
    'reservation' => [
        'duration_minutes' => 90,
        'slot_interval_minutes' => 30,
        'max_party_size' => 10,
        'min_advance_booking_hours' => 2,
        'max_advance_booking_days' => 30,
        'require_deposit' => false,
        'deposit_amount' => 0,
        'confirmation_required' => true,
    ],

    // Order Settings
    'order' => [
        'delivery_charge' => env('DELIVERY_CHARGE', 300),
        'free_delivery_threshold' => env('FREE_DELIVERY_THRESHOLD', 50.00),
        'tax_rate' => env('TAX_RATE', 8), // percent
        'service_charge_rate' => env('SERVICE_CHARGE_RATE', 10), // percent
        'min_order_amount' => env('MIN_ORDER_AMOUNT', 10.00),
        'max_order_amount' => env('MAX_ORDER_AMOUNT', 500.00),
        'preparation_time_default' => 30, // minutes
        'delivery_time_default' => 45, // minutes
    ],

    // Payment Settings
    'payment' => [
        'accepted_methods' => ['cash', 'card', 'cash_on_delivery', 'online'],
        'currency' => 'LKR',
        'currency_symbol' => 'LKR ',
        'decimal_places' => 2,
    ],

    // Menu Settings
    'menu' => [
        'items_per_page' => 12,
        'max_custom_ingredients' => 5,
        'allow_special_instructions' => true,
        'max_special_instructions_length' => 500,
    ],

    // Inventory Settings
    'inventory' => [
        'low_stock_threshold' => 10,
        'auto_reorder' => false,
        'reorder_quantity' => 50,
    ],

    // Session Settings
    'session' => [
        'dining_timeout_minutes' => 180, // 3 hours max for dining
        'cart_expiry_hours' => 24,
        'order_expiry_minutes' => 15, // For pending orders
    ],

    // Loyalty Program
    'loyalty' => [
        'enabled' => true,
        'points_per_rupee' => 1, // 1 point per Rs. 1
        'points_to_rupee_ratio' => 100, // 100 points = Rs. 1
        'minimum_redemption_points' => 500,
    ],

    // Notification Settings
    'notifications' => [
        'email_enabled' => true,
        'sms_enabled' => false,
        'order_notifications' => true,
        'reservation_notifications' => true,
        'marketing_emails' => false,
    ],

    // Social Media Links
    'social' => [
        'facebook' => env('SOCIAL_FACEBOOK', ''),
        'instagram' => env('SOCIAL_INSTAGRAM', ''),
        'twitter' => env('SOCIAL_TWITTER', ''),
        'linkedin' => env('SOCIAL_LINKEDIN', ''),
    ],

    // Image Settings
    'images' => [
        'max_upload_size' => 2048, // KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
        'menu_thumbnail_size' => [300, 300],
        'menu_large_size' => [800, 600],
    ],
];
