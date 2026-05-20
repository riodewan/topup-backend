<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'guest_email',
        'guest_phone',
        'product_id',
        'order_number',
        'target_id',
        'quantity',
        'price',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'payment_token',
        'payment_url',
        'provider_ref',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price'    => 'decimal:2',
            'total'    => 'decimal:2',
            'quantity' => 'integer',
            'status'   => 'string',
        ];
    }

    /**
     * Auto-generate order_number unik sebelum create.
     */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(10)) . '-' . now()->format('YmdHis');
            }
        });
    }

    /**
     * Relasi ke user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
