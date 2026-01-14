<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'image_url',
        'category_id',
        'is_featured',
        'price_cents',
        'promo_price_cents',
        'compare_at_price_cents',
        'description',
        'rating_avg',
        'rating_count',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
