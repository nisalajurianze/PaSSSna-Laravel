<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Inventory;
use App\Models\Promotion;
use App\Observers\MenuItemObserver;
use App\Observers\OrderObserver;
use App\Observers\ReservationObserver;
use App\Observers\InventoryObserver;
use App\Observers\PromotionObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any application services
    }

    public function boot(): void
    {
        // Set default string length for MySQL
        Schema::defaultStringLength(191);

        // Use Tailwind pagination styles (customized view)
        Paginator::useTailwind();

        // Share common data with all views
        View::composer('*', function ($view) {
            $view->with('restaurantName', 'PaSSSna Restaurant');
            $view->with('restaurantPhone', '+1 (555) 123-4567');
            $view->with('restaurantEmail', 'info@passsna.com');
            $view->with('restaurantAddress', '123 Gourmet Street, Food City');

            // Theme colors
            $view->with('themeColors', [
                'primary-red' => '#DC2626',
                'primary-yellow' => '#FBBF24',
                'navy-blue' => '#1E3A8A',
            ]);
        });

        // Custom Blade directives
        Blade::directive('money', function ($expression) {
            return "<?php echo '₹' . number_format($expression, 2); ?>";
        });

        Blade::directive('dateFormat', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->format('F j, Y, g:i a'); ?>";
        });

        Blade::directive('shortDate', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->format('M j, Y'); ?>";
        });

        Blade::directive('timeAgo', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->diffForHumans(); ?>";
        });

        Blade::directive('statusBadge', function ($expression) {
            return "<?php
                \$color = match(\$status = {$expression}) {
                    'pending', 'draft' => 'warning',
                    'confirmed', 'active', 'completed', 'approved' => 'success',
                    'cancelled', 'failed', 'rejected' => 'danger',
                    'preparing', 'processing' => 'info',
                    default => 'secondary'
                };
                echo '<span class=\"badge bg-' . \$color . '\">' . ucfirst(\$status) . '</span>';
            ?>";
        });

        Blade::directive('avatar', function ($expression) {
            return "<?php
                \$name = {$expression};
                \$initials = '';
                \$words = explode(' ', \$name);
                foreach (\$words as \$word) {
                    \$initials .= strtoupper(substr(\$word, 0, 1));
                }
                \$initials = substr(\$initials, 0, 2);
                echo '<span class=\"avatar-initials\">' . \$initials . '</span>';
            ?>";
        });

        // Register custom validation rules
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9]{10,15}$/', $value);
        }, 'The :attribute must be a valid phone number.');

        Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
        }, 'Password must be at least 8 characters with uppercase, lowercase, number and special character.');

        // Set application timezone
        date_default_timezone_set('Asia/Kolkata');

        // Register Model Observers for real-time events
        MenuItem::observe(MenuItemObserver::class);
        Order::observe(OrderObserver::class);
        Reservation::observe(ReservationObserver::class);
        Inventory::observe(InventoryObserver::class);
        Promotion::observe(PromotionObserver::class);
    }
}
