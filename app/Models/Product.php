<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $image = $this->relationLoaded('images')
            ? $this->images->first()
            : $this->images()->first();

        if ($image instanceof ProductImage) {
            return $image->image_url;
        }

        return $this->image_url;
    }

    /**
     * Retorna o preço efetivo (promocional se existir, senão o normal)
     */
    public function getEffectivePriceCentsAttribute(): int
    {
        return $this->promo_price_cents ?? $this->price_cents;
    }
}
