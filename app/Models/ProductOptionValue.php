<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionValue extends Model
{
    protected $fillable = ['option_id', 'value', 'price_modifier_cents', 'sort_order'];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'option_id');
    }

    /**
     * Retorna o modificador de preço formatado
     */
    public function getPriceModifierFormattedAttribute(): ?string
    {
        if ($this->price_modifier_cents === null) {
            return null;
        }
        
        return number_format($this->price_modifier_cents / 100, 2, ',', '.');
    }
}
