<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Security Headers
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\SecurityHeaders::class,

        // Trust Proxies
        \App\Http\Middleware\TrustProxies::class,

        // Validate Request Size
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trim Strings
        \App\Http\Middleware\TrimStrings::class,

        // Convert Empty Strings to Null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

        // Custom Application Middleware
        \App\Http\Middleware\CheckRestaurantOpen::class,
        \App\Http\Middleware\SessionTimeout::class,
        \App\Http\Middleware\PreventBackHistory::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // Cookie & Session
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // CSRF Protection
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,

            // Routing
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // Custom Middleware
            \App\Http\Middleware\RegistrationComplete::class,
        ],

        'api' => [
            // Enable rate limiting
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',

            // Binding resolution
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // Add API rate limiting per user if authenticated
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':200,1',
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,

        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Custom Middleware
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'customer' => \App\Http\Middleware\CustomerMiddleware::class,
        'dining.session' => \App\Http\Middleware\DiningSessionMiddleware::class,
        'cart.notempty' => \App\Http\Middleware\CartNotEmptyMiddleware::class,
        'reservation.time' => \App\Http\Middleware\ReservationTimeMiddleware::class,
        'table.availability' => \App\Http\Middleware\TableAvailabilityMiddleware::class,
        'check.role' => \App\Http\Middleware\CheckRole::class,
        'validate.promo' => \App\Http\Middleware\ValidatePromoCode::class,
        'verify.admin.password' => \App\Http\Middleware\VerifyAdminPassword::class,

        // API middleware
        'admin.api' => \App\Http\Middleware\AdminApiMiddleware::class,
    ];

    /**
     * The application's middleware priority.
     *
     * This determines the order in which middleware will be stacked.
     *
     * @var array<int, class-string>
     */
    protected $middlewarePriority = [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authenticate::class,
        \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        \App\Http\Middleware\AdminMiddleware::class,
    ];
}
