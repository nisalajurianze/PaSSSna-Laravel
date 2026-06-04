<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Messages
    |--------------------------------------------------------------------------
    |
    | Messages for email and system notifications.
    |
    */

    // Email Subjects
    'email_subjects' => [
        'welcome' => 'Welcome to PaSSSna Restaurant!',
        'order_confirmation' => 'Order Confirmation - PaSSSna Restaurant',
        'order_status_update' => 'Order Status Update - PaSSSna Restaurant',
        'reservation_confirmation' => 'Reservation Confirmation - PaSSSna Restaurant',
        'reservation_status_update' => 'Reservation Status Update - PaSSSna Restaurant',
        'password_reset' => 'Reset Your Password - PaSSSna Restaurant',
        'promo_code' => 'Special Offer from PaSSSna Restaurant',
        'newsletter' => 'Latest Updates from PaSSSna Restaurant',
        'review_request' => 'How Was Your Experience at PaSSSna?',
        'staff_schedule' => 'Your Schedule Update - PaSSSna Restaurant',
    ],

    // Email Messages
    'email_messages' => [
        'welcome' => [
            'subject' => 'Welcome to PaSSSna Restaurant!',
            'greeting' => 'Welcome :name!',
            'line1' => 'Thank you for registering with PaSSSna Restaurant.',
            'line2' => 'You can now enjoy:',
            'benefits' => [
                'Fast online ordering',
                'Easy table reservations',
                'Exclusive member offers',
                'Order history tracking',
                'Personalized recommendations',
            ],
            'action' => 'Start Ordering',
            'closing' => 'We look forward to serving you!',
        ],

        'order_confirmation' => [
            'subject' => 'Order Confirmation - Order #:order_number',
            'greeting' => 'Hello :name,',
            'line1' => 'Thank you for your order!',
            'line2' => 'Your order has been received and is being processed.',
            'details' => 'Order Details:',
            'action' => 'View Order',
            'closing' => 'We\'ll notify you once your order is ready.',
        ],

        'reservation_confirmation' => [
            'subject' => 'Reservation Confirmation - PaSSSna Restaurant',
            'greeting' => 'Hello :name,',
            'line1' => 'Your reservation has been confirmed!',
            'line2' => 'Reservation Details:',
            'action' => 'View Reservation',
            'closing' => 'We look forward to serving you!',
        ],

        'password_reset' => [
            'subject' => 'Reset Your Password',
            'greeting' => 'Hello!',
            'line1' => 'You are receiving this email because we received a password reset request for your account.',
            'action' => 'Reset Password',
            'line2' => 'This password reset link will expire in :count minutes.',
            'line3' => 'If you did not request a password reset, no further action is required.',
        ],
    ],

    // SMS Messages
    'sms_messages' => [
        'order_confirmation' => 'Your order #:order_number has been received. Estimated ready time: :time',
        'order_ready' => 'Your order #:order_number is ready for pickup/delivery.',
        'order_delivered' => 'Your order #:order_number has been delivered. Enjoy your meal!',
        'reservation_confirmation' => 'Your reservation for :date at :time for :people people is confirmed. Table #:table',
        'reservation_reminder' => 'Reminder: Your reservation at PaSSSna is in 1 hour.',
        'promo_code' => 'Special offer! Use code :code for :discount off your next order. Valid until :date',
    ],

    // Push Notifications
    'push_notifications' => [
        'new_order' => 'New order received - #:order_number',
        'order_status' => 'Your order status has been updated to :status',
        'reservation_status' => 'Your reservation status has been updated',
        'promotion' => 'New promotion available! :description',
        'low_stock' => 'Low stock alert for :item',
        'staff_schedule' => 'Your schedule has been updated for :date',
    ],

    // System Notifications
    'system_notifications' => [
        'new_customer' => 'New customer registered: :name',
        'new_order' => 'New order placed: #:order_number',
        'new_reservation' => 'New reservation made for :date',
        'low_stock' => 'Low stock alert: :item (Quantity: :quantity)',
        'table_occupied' => 'Table #:table is now occupied',
        'table_vacant' => 'Table #:table is now vacant',
        'staff_shift_start' => ':name\'s shift starts in 30 minutes',
        'staff_shift_end' => ':name\'s shift ends in 30 minutes',
        'birthday_alert' => 'Customer :name has birthday today',
        'anniversary_alert' => 'Customer :name has anniversary today',
        'review_received' => 'New review received from :name',
        'contact_message' => 'New contact message from :name',
    ],
];
