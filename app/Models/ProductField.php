<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductField extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_group_id',
        'name',
        'field_key',
        'type',
        'placeholder',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'product_group_id');
    }
}
