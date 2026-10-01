<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'number', 'status', 'locale', 'currency', 'subtotal', 'shipping', 'discount', 'total',
    'email', 'phone', 'shipping_address', 'stripe_session_id', 'stripe_payment_intent_id',
    'supplier_order_id', 'supplier_status', 'supplier_error', 'tracking_number', 'carrier',
    'paid_at', 'sent_to_supplier_at', 'shipped_at',
])]
class Order extends Model
{
    use HasFactory;

    protected $attributes = [
        'currency' => 'CHF',
        'discount' => 0,
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'shipping_address' => 'array',
            'paid_at' => 'datetime',
            'sent_to_supplier_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->number ??= 'HZ-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }
}
