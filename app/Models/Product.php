<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Product extends Model
{
    protected $fillable = [
        'name',
        'image_url',
        'category_id',
        'is_featured',
        'is_active',
        'has_variants',
        'size',
        'color',
        'price_cents',
        'promo_price_cents',
        'description',
        'rating_avg',
        'rating_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'has_variants' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        $relation = $this->hasMany(ProductImage::class);

        if (static::productImagesTableExists()) {
            if (static::productImagesSortOrderColumnExists()) {
                $relation->orderBy('sort_order');
            }
            $relation->orderBy('id');
        }

        return $relation;
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        if (! static::productImagesTableExists()) {
            return $this->image_url;
        }

        try {
            $image = $this->relationLoaded('images')
                ? $this->images->first()
                : $this->images()->first();

            if ($image instanceof ProductImage) {
                return $image->image_url;
            }

            return $this->image_url;
        } catch (\Throwable) {
            return $this->image_url;
        }
    }

    public static function productImagesTableExists(): bool
    {
        try {
            return Schema::hasTable('product_images');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function productImagesSortOrderColumnExists(): bool
    {
        if (! static::productImagesTableExists()) {
            return false;
        }

        try {
            return Schema::hasColumn('product_images', 'sort_order');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Retorna o preço efetivo (promocional se existir, senão o normal)
     */
    public function getEffectivePriceCentsAttribute(): int
    {
        return $this->promo_price_cents ?? $this->price_cents;
    }
}
