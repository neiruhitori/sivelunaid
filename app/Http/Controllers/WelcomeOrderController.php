<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductField;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WelcomeOrderController extends Controller
{
    private const PAYMENT_METHODS = [
        'QRIS',
        'Transfer Bank - SeaBank',
        'E-Wallet - DANA',
        'E-Wallet - GoPay',
        'E-Wallet - ShopeePay',
    ];

    public function history(Request $request): View|JsonResponse
    {
        $search = trim((string) $request->query('q', ''));
        $orders = Order::query()
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    foreach (['order_number', 'product_name', 'group_name', 'option_name', 'variant_name'] as $column) {
                        $query->orWhere($column, 'like', '%' . addcslashes($search, '%_\\\\') . '%');
                    }
                });
            })
            ->latest('completed_at')
            ->paginate(12)
            ->withQueryString();

        $privateFields = ProductField::query()
            ->whereIn('product_group_id', $orders->getCollection()->pluck('product_group_id')->filter()->unique())
            ->whereIn('type', ['password', 'backup_code'])
            ->get(['product_group_id', 'field_key', 'name'])
            ->groupBy('product_group_id');
        $orders->getCollection()->each(function (Order $order) use ($privateFields) {
            $order->setAttribute('public_customer_data', $order->publicCustomerData($privateFields->get($order->product_group_id, [])));
        });

        if ($request->ajax()) {
            return response()->json([
                'html' => view('welcome.partials.order-history-results', compact('orders', 'search'))->render(),
            ]);
        }

        return view('welcome.history', compact('orders', 'search'));
    }

    public function store(Request $request): JsonResponse
    {
        $base = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'payment_method' => ['required', 'string', 'in:' . implode(',', self::PAYMENT_METHODS)],
            'fields' => ['nullable', 'array'],
        ]);

        $variant = ProductVariant::query()
            ->with(['group.product.category', 'groupOption'])
            ->where('is_active', true)
            ->findOrFail($base['variant_id']);
        $group = $variant->group;
        $product = $group?->product;

        abort_unless(
            $group && $group->is_active && $product && $product->is_active
                && $product->category?->is_active
                && (! $group->has_options || ($variant->groupOption && $variant->groupOption->is_active)),
            422,
        );

        $submitted = $base['fields'] ?? [];
        $rules = [];
        foreach ($group->fields()->orderBy('sort_order')->orderBy('id')->get() as $field) {
            if ($field->type === 'backup_code') {
                foreach (range(1, 3) as $index) {
                    $rules["{$field->field_key}_{$index}"] = ['nullable', 'string', 'max:255'];
                }
                continue;
            }

            $fieldRules = [$field->is_required ? 'required' : 'nullable', 'string', 'max:2000'];
            if ($field->type === 'email') {
                $fieldRules[] = 'email';
                $fieldRules[] = 'max:255';
            } elseif ($field->type === 'number') {
                $fieldRules = [$field->is_required ? 'required' : 'nullable', 'numeric'];
            }
            $rules[$field->field_key] = $fieldRules;
        }

        $validatedFields = Validator::make($submitted, $rules)->validate();
        $customerData = [];
        foreach ($group->fields()->orderBy('sort_order')->orderBy('id')->get() as $field) {
            if ($field->type === 'backup_code') {
                foreach (range(1, 3) as $index) {
                    $value = trim((string) ($validatedFields["{$field->field_key}_{$index}"] ?? ''));
                    if ($value !== '') {
                        $customerData[] = [
                            'label' => "{$field->name} {$index}",
                            'field_key' => $field->field_key,
                            'type' => $field->type,
                            'value' => $value,
                        ];
                    }
                }
                continue;
            }

            $value = trim((string) ($validatedFields[$field->field_key] ?? ''));
            if ($value !== '') {
                $customerData[] = [
                    'label' => $field->name,
                    'field_key' => $field->field_key,
                    'type' => $field->type,
                    'value' => $value,
                ];
            }
        }

        $price = $variant->discount_price ?? $variant->price;
        $order = Order::query()->create([
            'order_number' => 'VL' . now()->format('ymd') . Str::upper(Str::random(8)),
            'product_id' => $product->id,
            'product_group_id' => $group->id,
            'product_group_option_id' => $variant->product_group_option_id,
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'group_name' => $group->name,
            'option_name' => $variant->groupOption?->name,
            'variant_name' => $variant->name,
            'customer_data' => $customerData,
            'total' => $price,
            'payment_method' => $base['payment_method'],
            'status' => 'pending_payment',
        ]);

        return response()->json([
            'order_number' => $order->order_number,
            'whatsapp_url' => $this->whatsappUrl($order),
        ], 201);
    }

    private function whatsappUrl(Order $order): string
    {
        $instructions = match ($order->payment_method) {
            'QRIS' => [
                '୨୧ *PEMBAYARAN QRIS*',
                'Gambar QRIS akan dikirimkan oleh admin melalui chat ini setelah pesanan dikonfirmasi.',
            ],
            'Transfer Bank - SeaBank' => [
                '୨୧ *PEMBAYARAN SEABANK*',
                'Nomor rekening: 901318468036',
            ],
            'E-Wallet - DANA' => ['୨୧ *PEMBAYARAN DANA*', 'Nomor: +62 852-3649-6518'],
            'E-Wallet - GoPay' => ['୨୧ *PEMBAYARAN GOPAY*', 'Nomor: +62 852-3649-6518'],
            default => ['୨୧ *PEMBAYARAN SHOPEEPAY*', 'Nomor: +62 852-3649-6518'],
        };

        $lines = [
            '୨୧ 𓂃 🌙 𐙚',
            '*V E L U N A  S T O R E*',
            '',
            '🧾 *KONFIRMASI PESANAN*',
            "Nomor pesanan: {$order->order_number}",
            '',
            '━━━━━━━━━━━━━━',
            '',
            'Halo Veluna, saya ingin memesan:',
            "୨୧ Produk: {$order->product_name}",
            "୨୧ Pilihan: {$order->group_name}",
        ];

        if ($order->option_name) {
            $lines[] = "୨୧ Subpilihan: {$order->option_name}";
        }
        $lines[] = "୨୧ Paket: {$order->variant_name}";
        foreach ($order->customer_data ?? [] as $field) {
            $lines[] = "୨୧ {$field['label']}: {$field['value']}";
        }
        $lines = array_merge($lines, [
            "୨୧ Pembayaran: {$order->payment_method}",
            '',
            '💌 *TOTAL PEMBAYARAN*',
            '*Rp' . number_format((float) $order->total, 0, ',', '.') . '*',
            '',
        ], $instructions, [
            '',
            '♡ Mohon konfirmasi pesanan dan pembayaran ini.',
            '♡ Saya akan mengirim bukti pembayaran di chat ini.',
            '',
            'Terima kasih 💖',
        ]);

        return 'https://wa.me/6738386518?text=' . urlencode(implode("\n", $lines));
    }
}
