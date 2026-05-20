<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'provider',
        'provider_code',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price'  => 'decimal:2',
            'stock'  => 'integer',
            'type'   => 'string',
            'status' => 'string',
        ];
    }

    /**
     * Auto-generate slug sebelum create jika tidak di-set.
     */
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Scope: hanya produk aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Relasi ke game (induk).
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Relasi ke orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
