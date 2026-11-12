<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value'];

    public $timestamps = false;

    public static function storefrontIsOpen(): bool
    {
        return Cache::remember('site.storefront_is_open', now()->addMinute(), function () {
            return filter_var(static::query()->whereKey('storefront_open')->value('value') ?? '1', FILTER_VALIDATE_BOOL);
        });
    }

    public static function setStorefrontOpen(bool $isOpen): void
    {
        static::query()->updateOrCreate(
            ['key' => 'storefront_open'],
            ['value' => $isOpen ? '1' : '0'],
        );

        Cache::forget('site.storefront_is_open');
    }
}
