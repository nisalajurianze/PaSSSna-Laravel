<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'action_url',
        'priority',
        'channel',
        'is_sent',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    // Notification Types (must match the DB enum)
    public const TYPE_ORDER = 'order';
    public const TYPE_RESERVATION = 'reservation';
    public const TYPE_INVENTORY = 'inventory';
    public const TYPE_SYSTEM = 'system';
    public const TYPE_PROMOTION = 'promotion';
    public const TYPE_PAYMENT = 'payment';

    // Priority Levels (must match the DB enum)
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // Channels (must match the DB enum)
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_SMS = 'sms';
    public const CHANNEL_IN_APP = 'in_app';
    public const CHANNEL_ALL = 'all';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Accessors (compatibility helpers used by existing blades)
    public function getTimeAgoAttribute(): ?string
    {
        return $this->created_at?->diffForHumans();
    }

    public function excerpt(int $length = 50): string
    {
        $message = (string) ($this->message ?? '');
        return strlen($message) > $length ? substr($message, 0, $length) . '...' : $message;
    }

    public function getActionTextAttribute(): ?string
    {
        return $this->data['action_text'] ?? null;
    }

    public function getRelatedTypeAttribute(): ?string
    {
        return $this->data['related']['type'] ?? null;
    }

    public function getRelatedIdAttribute(): ?int
    {
        $value = $this->data['related']['id'] ?? null;
        return $value !== null ? (int) $value : null;
    }

    public function getTypeColorAttribute(): string
    {
        return match ((string) $this->type) {
            self::TYPE_ORDER => 'blue',
            self::TYPE_RESERVATION => 'green',
            self::TYPE_INVENTORY => 'yellow',
            self::TYPE_PROMOTION => 'purple',
            self::TYPE_PAYMENT => 'indigo',
            self::TYPE_SYSTEM => 'gray',
            default => 'gray',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ((string) $this->type) {
            self::TYPE_ORDER => 'fas fa-shopping-cart',
            self::TYPE_RESERVATION => 'fas fa-calendar-check',
            self::TYPE_INVENTORY => 'fas fa-box',
            self::TYPE_PROMOTION => 'fas fa-tag',
            self::TYPE_PAYMENT => 'fas fa-credit-card',
            self::TYPE_SYSTEM => 'fas fa-cog',
            default => 'fas fa-bell',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ((string) $this->priority) {
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_MEDIUM => 'blue',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_URGENT => 'red',
            default => 'gray',
        };
    }

    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    public function isRead(): bool
    {
        return (bool) $this->is_read;
    }

    public function markAsRead(): void
    {
        if ($this->is_read) {
            return;
        }

        $this->forceFill([
            'is_read' => true,
            'read_at' => $this->read_at ?? now(),
        ])->save();
    }

    public function markAsUnread(): void
    {
        $this->forceFill([
            'is_read' => false,
            'read_at' => null,
        ])->save();
    }

    // Convenience factories used in a few places (store "related" info inside data JSON).
    public static function createOrderNotification(int $userId, Order $order, string $title, string $message)
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_ORDER,
            'title' => $title,
            'message' => $message,
            'data' => [
                'related' => ['type' => 'order', 'id' => $order->id],
                'order_id' => $order->id,
                'action_text' => 'View Order',
            ],
            'action_url' => route('admin.orders.show', $order->id),
            'priority' => self::PRIORITY_MEDIUM,
            'channel' => self::CHANNEL_IN_APP,
        ]);
    }

    public static function createReservationNotification(int $userId, Reservation $reservation, string $title, string $message)
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_RESERVATION,
            'title' => $title,
            'message' => $message,
            'data' => [
                'related' => ['type' => 'reservation', 'id' => $reservation->id],
                'reservation_id' => $reservation->id,
                'action_text' => 'View Reservation',
            ],
            'action_url' => route('admin.reservations.show', $reservation->id),
            'priority' => self::PRIORITY_MEDIUM,
            'channel' => self::CHANNEL_IN_APP,
        ]);
    }

    public static function createLowStockNotification(int $userId, Inventory $inventoryItem)
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_INVENTORY,
            'title' => 'Low Stock Alert',
            'message' => $inventoryItem->item_name . ' is running low. Current stock: ' . $inventoryItem->current_stock,
            'data' => [
                'related' => ['type' => 'inventory', 'id' => $inventoryItem->id],
                'inventory_id' => $inventoryItem->id,
                'action_text' => 'View Inventory',
            ],
            'action_url' => route('admin.inventory.index'),
            'priority' => self::PRIORITY_HIGH,
            'channel' => self::CHANNEL_IN_APP,
        ]);
    }

    public static function createPromotionNotification(int $userId, Promotion $promotion)
    {
        $code = $promotion->promo_code ?: ($promotion->code ?? 'PROMO');
        $discountType = $promotion->discount_type ?? $promotion->type ?? 'percentage';
        $discountValue = (float) ($promotion->discount_value ?? 0);
        $symbol = config('restaurant.payment.currency_symbol', 'LKR ');

        $discountText = $discountType === 'percentage'
            ? $discountValue . '% off'
            : $symbol . number_format($discountValue, 2) . ' off';

        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_PROMOTION,
            'title' => 'New Promotion Available',
            'message' => 'Use code ' . $code . ' to get ' . $discountText,
            'data' => [
                'related' => ['type' => 'promotion', 'id' => $promotion->id],
                'promotion_id' => $promotion->id,
                'action_text' => 'Shop Now',
            ],
            'action_url' => route('menu'),
            'priority' => self::PRIORITY_LOW,
            'channel' => self::CHANNEL_IN_APP,
        ]);
    }

    /**
     * Create customer-facing order status notification
     */
    public static function createCustomerOrderNotification(Order $order, string $oldStatus, string $newStatus)
    {
        $statusMessages = [
            'confirmed' => 'Your order has been confirmed and will be prepared shortly.',
            'preparing' => 'Great news! Your order is now being prepared by our kitchen.',
            'ready' => 'Your order is ready! Please collect it from the counter.',
            'out_for_delivery' => 'Your order is on its way! Our driver will arrive soon.',
            'delivered' => 'Your order has been delivered. Enjoy your meal!',
            'served' => 'Your food has been served. Enjoy your meal!',
            'completed' => 'Your order has been completed. Thank you for dining with us!',
            'cancelled' => 'Your order has been cancelled. Contact us for any questions.',
        ];

        $title = 'Order Update - ' . ucfirst(str_replace('_', ' ', $newStatus));
        $message = $statusMessages[$newStatus] ?? 'Your order status has been updated to ' . str_replace('_', ' ', $newStatus);

        return self::create([
            'user_id' => $order->user_id,
            'type' => self::TYPE_ORDER,
            'title' => $title,
            'message' => $message,
            'data' => [
                'related' => ['type' => 'order', 'id' => $order->id],
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'action_text' => 'View Order',
            ],
            'action_url' => route('customer.orders.show', $order->id),
            'priority' => self::PRIORITY_MEDIUM,
            'channel' => self::CHANNEL_IN_APP,
        ]);
    }
}
