<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Custom messages for PaSSSna
    'login' => [
        'title' => 'Login to PaSSSna',
        'email' => 'Email Address',
        'password' => 'Password',
        'remember' => 'Remember Me',
        'forgot' => 'Forgot Your Password?',
        'button' => 'Login',
        'no_account' => 'Don\'t have an account?',
        'register' => 'Register here',
        'admin_detected' => 'Admin access detected. Redirecting to admin panel.',
        'customer_detected' => 'Customer access detected. Redirecting to customer dashboard.',
    ],

    'register' => [
        'title' => 'Create Account',
        'name' => 'Full Name',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'address' => 'Address',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'button' => 'Register',
        'already_account' => 'Already have an account?',
        'login' => 'Login here',
        'success' => 'Account created successfully! Please login.',
    ],

    'logout' => [
        'success' => 'Successfully logged out.',
    ],

    'verify' => [
        'title' => 'Verify Your Email Address',
        'sent' => 'A fresh verification link has been sent to your email address.',
        'check' => 'Before proceeding, please check your email for a verification link.',
        'not_receive' => 'If you did not receive the email',
        'resend' => 'click here to request another',
    ],

    'passwords' => [
        'reset' => 'Your password has been reset!',
        'sent' => 'We have emailed your password reset link!',
        'throttled' => 'Please wait before retrying.',
        'token' => 'This password reset token is invalid.',
        'user' => "We can't find a user with that email address.",
    ],
];
