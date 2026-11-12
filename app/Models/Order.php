<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class Order extends Model
{
    public const STATUSES = [
        'pending_payment' => 'Menunggu Pembayaran',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    protected $fillable = [
        'order_number',
        'product_id',
        'product_group_id',
        'product_group_option_id',
        'product_variant_id',
        'product_name',
        'group_name',
        'option_name',
        'variant_name',
        'customer_data',
        'total',
        'payment_method',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function getCustomerDataAttribute(?string $value): array
    {
        $fields = json_decode($value ?? '[]', true) ?: [];
        foreach ($fields as &$field) {
            if (! isset($field['value'])) {
                continue;
            }

            try {
                $field['value'] = Crypt::decryptString($field['value']);
            } catch (Throwable) {
                // Keep legacy or malformed values readable rather than hiding an order detail.
            }
        }
        unset($field);

        return $fields;
    }

    public function setCustomerDataAttribute(?array $fields): void
    {
        $fields = $fields ?? [];
        foreach ($fields as &$field) {
            if (isset($field['value'])) {
                $field['value'] = Crypt::encryptString((string) $field['value']);
            }
        }
        unset($field);

        $this->attributes['customer_data'] = json_encode($fields, JSON_THROW_ON_ERROR);
    }

    public function publicCustomerData(iterable $privateFields = []): array
    {
        $privateKeys = [];
        $privateNames = [];
        foreach ($privateFields as $privateField) {
            $privateKeys[] = strtolower((string) $privateField->field_key);
            $privateNames[] = strtolower(trim((string) $privateField->name));
        }

        $visible = [];
        foreach ($this->customer_data as $field) {
            $type = strtolower((string) ($field['type'] ?? ''));
            $key = strtolower((string) ($field['field_key'] ?? $field['key'] ?? ''));
            $label = trim((string) ($field['label'] ?? ''));
            $normalizedLabel = strtolower($label);
            $isPrivate = in_array($type, ['password', 'backup_code'], true)
                || in_array($key, $privateKeys, true)
                || preg_match('/password|backup[\s_-]*code/i', $label) === 1;

            if (! $isPrivate) {
                foreach ($privateNames as $privateName) {
                    if ($privateName !== '' && ($normalizedLabel === $privateName || str_starts_with($normalizedLabel, $privateName . ' '))) {
                        $isPrivate = true;
                        break;
                    }
                }
            }

            if ($isPrivate) {
                continue;
            }

            $value = trim((string) ($field['value'] ?? ''));
            if ($value === '') {
                continue;
            }

            $characters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $maskedValue = count($characters) < 3
                ? '****'
                : $characters[0] . '****' . $characters[count($characters) - 1];

            $visible[] = ['label' => $label, 'value' => $maskedValue];
        }

        return $visible;
    }
}
