<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_group_id',
        'product_group_option_id',
        'name',
        'description',
        'price',
        'discount_price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'product_group_id');
    }

    public function groupOption(): BelongsTo
    {
        return $this->belongsTo(ProductGroupOption::class, 'product_group_option_id');
    }
}
