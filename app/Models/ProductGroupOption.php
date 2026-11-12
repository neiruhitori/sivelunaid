<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductGroupOption extends Model
{
    protected $fillable = ['product_group_id', 'name', 'description', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean', 'sort_order' => 'integer'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'product_group_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
