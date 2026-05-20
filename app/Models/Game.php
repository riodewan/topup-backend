<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Game extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'banner',
        'description',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type'   => 'string',
            'status' => 'string',
        ];
    }

    /**
     * Auto-generate slug sebelum create jika tidak di-set.
     */
    protected static function booted(): void
    {
        static::creating(function (Game $game) {
            if (empty($game->slug)) {
                $game->slug = Str::slug($game->name);
            }
        });
    }

    /**
     * Scope: hanya game aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Relasi ke products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relasi ke active products saja.
     */
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('status', 'active');
    }
}
