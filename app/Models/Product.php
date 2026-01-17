<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Product extends Model
{
    /**
     * Override the connection to use tenant connection dynamically
     */
    public function getConnectionName(): ?string
    {
        return config('tenancy.tenant_connection', config('database.default'));
    }
    protected $fillable = [
        'name',
        'slug',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = static::generateUniqueSlug($product->name);
        });

        static::updating(function ($product) {
             if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
             }
        });
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Ensure uniqueness
        while (static::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = $originalSlug . '-' . ++$count;
        }

        return $slug;
    }

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

    protected static ?bool $productImagesTableExistsCache = null;
    protected static ?bool $productImagesSortOrderColumnExistsCache = null;

    public static function productImagesTableExists(): bool
    {
        if (static::$productImagesTableExistsCache !== null) {
            return static::$productImagesTableExistsCache;
        }

        try {
            static::$productImagesTableExistsCache = Schema::hasTable('product_images');
        } catch (\Throwable) {
            static::$productImagesTableExistsCache = false;
        }

        return static::$productImagesTableExistsCache;
    }

    public static function productImagesSortOrderColumnExists(): bool
    {
        if (static::$productImagesSortOrderColumnExistsCache !== null) {
            return static::$productImagesSortOrderColumnExistsCache;
        }

        if (! static::productImagesTableExists()) {
            static::$productImagesSortOrderColumnExistsCache = false;
            return false;
        }

        try {
            static::$productImagesSortOrderColumnExistsCache = Schema::hasColumn('product_images', 'sort_order');
        } catch (\Throwable) {
            static::$productImagesSortOrderColumnExistsCache = false;
        }

        return static::$productImagesSortOrderColumnExistsCache;
    }

    /**
     * Retorna o preço efetivo (promocional se existir, senão o normal)
     */
    public function getEffectivePriceCentsAttribute(): int
    {
        return $this->promo_price_cents ?? $this->price_cents;
    }
}
