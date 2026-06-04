<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    public const METHOD_CASH = 'cash';
    public const METHOD_CARD = 'card';
    public const METHOD_COD = 'cash_on_delivery';
    public const METHOD_ONLINE = 'online';
    public const METHOD_MOBILE_WALLET = 'mobile_wallet';

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
        'status',
        'amount',
        'refunded_amount',
        'currency',
        'payment_details',
        'card_last_four',
        'card_brand',
        'payment_date',
        'refund_date',
        'payment_notes',
        'receipt_url',
        'payer_name',
        'payer_email',
        'payer_phone',
        'gateway_response_code',
        'gateway_response_message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'payment_details' => 'array',
        'payment_date' => 'datetime',
        'refund_date' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_REFUNDED => 'gray',
            self::STATUS_PARTIALLY_REFUNDED => 'orange',
            default => 'gray',
        };
    }

    public function getPaymentMethodTextAttribute(): string
    {
        return match ($this->payment_method) {
            self::METHOD_CASH => 'Cash',
            self::METHOD_CARD => 'Card',
            self::METHOD_COD => 'Cash on Delivery',
            self::METHOD_ONLINE => 'Online',
            self::METHOD_MOBILE_WALLET => 'Mobile Wallet',
            default => ucwords(str_replace('_', ' ', (string) $this->payment_method)),
        };
    }
}

