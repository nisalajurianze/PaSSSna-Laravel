<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Your password has been reset!',
    'sent' => 'We have emailed your password reset link!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",

    // Custom PaSSSna password messages
    'forgot_password' => [
        'title' => 'Forgot Your Password?',
        'instructions' => 'Enter your email address and we will send you a link to reset your password.',
        'email' => 'Email Address',
        'button' => 'Send Password Reset Link',
        'back_to_login' => 'Back to Login',
        'success' => 'Password reset link sent to your email!',
    ],

    'reset_password' => [
        'title' => 'Reset Password',
        'email' => 'Email Address',
        'password' => 'New Password',
        'confirm_password' => 'Confirm New Password',
        'button' => 'Reset Password',
        'requirements' => 'Password must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        'success' => 'Password reset successfully! You can now login with your new password.',
    ],

    'change_password' => [
        'title' => 'Change Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm New Password',
        'button' => 'Update Password',
        'success' => 'Password updated successfully!',
        'mismatch' => 'Current password does not match our records.',
    ],

    'validation' => [
        'min' => 'Password must be at least :min characters.',
        'mixed' => 'Password must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'Password must contain at least one number.',
        'symbols' => 'Password must contain at least one symbol.',
        'same' => 'Password confirmation does not match.',
    ],
];
