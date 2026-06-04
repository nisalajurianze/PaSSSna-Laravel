<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute must only contain letters.',
    'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute must only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'ascii' => 'The :attribute must only contain single-byte alphanumeric characters and symbols.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'array' => 'The :attribute must have between :min and :max items.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'numeric' => 'The :attribute must be between :min and :max.',
        'string' => 'The :attribute must be between :min and :max characters.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'decimal' => 'The :attribute must have :decimal decimal places.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'doesnt_end_with' => 'The :attribute may not end with one of the following: :values.',
    'doesnt_start_with' => 'The :attribute may not start with one of the following: :values.',
    'email' => 'The :attribute must be a valid email address.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'The selected :attribute is invalid.',
    'extensions' => 'The :attribute must have one of the following extensions: :values.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'array' => 'The :attribute must have more than :value items.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'numeric' => 'The :attribute must be greater than :value.',
        'string' => 'The :attribute must be greater than :value characters.',
    ],
    'gte' => [
        'array' => 'The :attribute must have :value items or more.',
        'file' => 'The :attribute must be greater than or equal to :value kilobytes.',
        'numeric' => 'The :attribute must be greater than or equal to :value.',
        'string' => 'The :attribute must be greater than or equal to :value characters.',
    ],
    'hex_color' => 'The :attribute must be a valid hexadecimal color.',
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lowercase' => 'The :attribute must be lowercase.',
    'lt' => [
        'array' => 'The :attribute must have less than :value items.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'numeric' => 'The :attribute must be less than :value.',
        'string' => 'The :attribute must be less than :value characters.',
    ],
    'lte' => [
        'array' => 'The :attribute must have :value items or less.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'array' => 'The :attribute must not have more than :max items.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute must not be greater than :max.',
        'string' => 'The :attribute must not be greater than :max characters.',
    ],
    'max_digits' => 'The :attribute must not have more than :max digits.',
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'array' => 'The :attribute must have at least :min items.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'numeric' => 'The :attribute must be at least :min.',
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'min_digits' => 'The :attribute must have at least :min digits.',
    'missing' => 'The :attribute field must be missing.',
    'missing_if' => 'The :attribute field must be missing when :other is :value.',
    'missing_unless' => 'The :attribute field must be missing unless :other is :value.',
    'missing_with' => 'The :attribute field must be missing when :values is present.',
    'missing_with_all' => 'The :attribute field must be missing when :values are present.',
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'password' => [
        'letters' => 'The :attribute must contain at least one letter.',
        'mixed' => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute must contain at least one number.',
        'symbols' => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => 'The :attribute field must be present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute must match :other.',
    'size' => [
        'array' => 'The :attribute must contain :size items.',
        'file' => 'The :attribute must be :size kilobytes.',
        'numeric' => 'The :attribute must be :size.',
        'string' => 'The :attribute must be :size characters.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'uppercase' => 'The :attribute must be uppercase.',
    'url' => 'The :attribute must be a valid URL.',
    'ulid' => 'The :attribute must be a valid ULID.',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],

        // PaSSSna Custom Validation Messages
        'name' => [
            'required' => 'Please enter your full name.',
            'min' => 'Name must be at least 3 characters.',
            'max' => 'Name cannot exceed 100 characters.',
        ],

        'email' => [
            'required' => 'Email address is required.',
            'email' => 'Please enter a valid email address.',
            'unique' => 'This email is already registered.',
        ],

        'phone' => [
            'required' => 'Phone number is required.',
            'regex' => 'Please enter a valid phone number.',
        ],

        'password' => [
            'required' => 'Password is required.',
            'min' => 'Password must be at least 8 characters.',
            'confirmed' => 'Password confirmation does not match.',
        ],

        'address' => [
            'required' => 'Address is required for delivery orders.',
            'min' => 'Address must be at least 10 characters.',
        ],

        'reservation_date' => [
            'required' => 'Please select a reservation date.',
            'after_or_equal' => 'Reservation date cannot be in the past.',
        ],

        'reservation_time' => [
            'required' => 'Please select a reservation time.',
        ],

        'people' => [
            'required' => 'Please specify number of people.',
            'min' => 'Minimum 1 person required.',
            'max' => 'Maximum :max people allowed per reservation.',
        ],

        'table_number' => [
            'required' => 'Please select a table.',
        ],

        'menu_item_id' => [
            'required' => 'Please select a menu item.',
            'exists' => 'Selected item is not available.',
        ],

        'quantity' => [
            'required' => 'Please specify quantity.',
            'min' => 'Minimum quantity is 1.',
            'max' => 'Maximum quantity is 10.',
        ],

        'payment_method' => [
            'required' => 'Please select a payment method.',
            'in' => 'Selected payment method is not valid.',
        ],

        'card_number' => [
            'required_if' => 'Card number is required for card payments.',
            'digits' => 'Card number must be 16 digits.',
        ],

        'card_expiry' => [
            'required_if' => 'Card expiry date is required.',
            'regex' => 'Please enter expiry date in MM/YY format.',
        ],

        'card_cvc' => [
            'required_if' => 'CVC is required.',
            'digits' => 'CVC must be 3 digits.',
        ],

        'image' => [
            'image' => 'File must be an image.',
            'mimes' => 'Image must be jpeg, png, jpg, or gif.',
            'max' => 'Image size cannot exceed 2MB.',
        ],

        'price' => [
            'required' => 'Price is required.',
            'numeric' => 'Price must be a number.',
            'min' => 'Price cannot be negative.',
        ],

        'category' => [
            'required' => 'Please select a category.',
            'in' => 'Selected category is not valid.',
        ],

        'stock_quantity' => [
            'required' => 'Stock quantity is required.',
            'integer' => 'Stock quantity must be a whole number.',
            'min' => 'Stock quantity cannot be negative.',
        ],

        'staff_role' => [
            'required' => 'Please select a role.',
            'in' => 'Selected role is not valid.',
        ],

        'shift' => [
            'required' => 'Please select a shift.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        // General
        'name' => 'full name',
        'email' => 'email address',
        'password' => 'password',
        'password_confirmation' => 'password confirmation',
        'current_password' => 'current password',
        'phone' => 'phone number',
        'address' => 'address',
        'city' => 'city',
        'state' => 'state',
        'zip_code' => 'zip code',
        'country' => 'country',

        // Restaurant Specific
        'restaurant_name' => 'restaurant name',
        'description' => 'description',
        'price' => 'price',
        'category' => 'category',
        'image' => 'image',
        'is_available' => 'availability',
        'offer_price' => 'offer price',
        'offer_valid_until' => 'offer valid until',
        'preparation_time' => 'preparation time',

        // Reservation
        'reservation_date' => 'reservation date',
        'reservation_time' => 'reservation time',
        'people' => 'number of people',
        'table_number' => 'table number',
        'special_requests' => 'special requests',

        // Order
        'order_type' => 'order type',
        'delivery_address' => 'delivery address',
        'payment_method' => 'payment method',
        'card_number' => 'card number',
        'card_expiry' => 'card expiry',
        'card_cvc' => 'CVC',
        'card_holder' => 'card holder name',
        'special_instructions' => 'special instructions',
        'promo_code' => 'promo code',

        // Staff
        'staff_name' => 'staff name',
        'staff_email' => 'staff email',
        'staff_phone' => 'staff phone',
        'staff_role' => 'role',
        'staff_shift' => 'shift',
        'staff_salary' => 'salary',

        // Inventory
        'item_name' => 'item name',
        'item_category' => 'item category',
        'stock_quantity' => 'stock quantity',
        'minimum_quantity' => 'minimum quantity',
        'unit' => 'unit',
        'supplier' => 'supplier',

        // Contact
        'subject' => 'subject',
        'message' => 'message',
        'rating' => 'rating',
        'review' => 'review',

        // Custom Meal
        'base_type' => 'base type',
        'meat_type' => 'meat type',
        'vegetables' => 'vegetables',
        'sauce' => 'sauce',
        'spice_level' => 'spice level',

        // Tables
        'table_capacity' => 'table capacity',
        'table_location' => 'table location',
        'table_status' => 'table status',
    ],

    /*
    |--------------------------------------------------------------------------
    | PaSSSna Specific Validation Messages
    |--------------------------------------------------------------------------
    */

    'paSSSna' => [
        'business_hours' => 'We are closed on :day. Please select another date.',
        'table_unavailable' => 'Selected table is not available at this time.',
        'min_reservation_time' => 'Reservations must be made at least 2 hours in advance.',
        'max_reservation_people' => 'Maximum :max people allowed for online reservations. For larger groups, please call us.',
        'delivery_not_available' => 'Delivery is not available for this area.',
        'cash_on_delivery_only' => 'Cash on delivery is only available for delivery orders.',
        'minimum_order_amount' => 'Minimum order amount is $:min.',
        'promo_code_expired' => 'This promo code has expired.',
        'promo_code_invalid' => 'Invalid promo code.',
        'promo_code_used' => 'You have already used this promo code.',
        'insufficient_stock' => 'Insufficient stock for :item. Only :quantity available.',
        'item_unavailable' => 'This item is currently unavailable.',
        'table_already_occupied' => 'This table is currently occupied.',
        'dining_password_required' => 'Admin password is required to exit dining section.',
        'invalid_dining_password' => 'Invalid admin password.',
        'custom_meal_ingredients' => 'Please select at least one ingredient for your custom meal.',
    ],
];
