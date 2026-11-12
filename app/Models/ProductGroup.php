<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'description',
        'banner_description',
        'has_options',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_options' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ProductField::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductGroupOption::class)->orderBy('sort_order')->orderBy('id');
    }
}
