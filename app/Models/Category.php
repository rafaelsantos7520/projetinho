<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * Override the connection to use tenant connection dynamically
     */
    public function getConnectionName(): ?string
    {
        return config('tenancy.tenant_connection', config('database.default'));
    }

    protected $fillable = ['name', 'slug', 'image_url', 'is_active', 'is_default', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
