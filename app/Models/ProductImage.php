<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    public function getConnectionName(): ?string
    {
        return config('tenancy.tenant_connection', config('database.default'));
    }

    protected $fillable = [
        'product_id',
        'image_url',
        'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

