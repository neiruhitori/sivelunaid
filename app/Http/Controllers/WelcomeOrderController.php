<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductField;
use App\Models\ProductVariant;
use App\Models\User;
use App\Mail\NewOrderAdminNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

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
            'product_group_id' => ['required', 'integer', 'exists:product_groups,id'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.variant_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'payment_method' => ['required', 'string', 'in:' . implode(',', self::PAYMENT_METHODS)],
            'fields' => ['nullable', 'array'],
        ]);

        $requestedItems = collect($base['items']);
        $variants = ProductVariant::query()
            ->with(['group.product.category', 'groupOption'])
            ->where('is_active', true)
            ->where('product_group_id', $base['product_group_id'])
            ->whereIn('id', $requestedItems->pluck('variant_id'))
            ->get()
            ->keyBy('id');

        if ($variants->count() !== $requestedItems->count()) {
            return response()->json(['message' => 'Ada paket yang sudah tidak tersedia. Muat ulang halaman lalu coba lagi.'], 422);
        }

        $firstVariant = $variants->get($requestedItems->first()['variant_id']);
        $group = $firstVariant?->group;
        $product = $group?->product;

        abort_unless(
            $group && (int) $group->id === (int) $request->input('product_group_id')
                && $group->is_active && $product && $product->is_active
                && $product->category?->is_active
                && $variants->every(fn (ProductVariant $variant) => ! $group->has_options
                    || ($variant->groupOption && $variant->groupOption->is_active)),
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

        $orderItems = $requestedItems->map(function (array $item) use ($variants) {
            /** @var ProductVariant $variant */
            $variant = $variants->get($item['variant_id']);
            $unitPrice = (float) ($variant->discount_price ?? $variant->price);
            $quantity = (int) $item['quantity'];

            return [
                'variant_id' => (int) $variant->id,
                'option_id' => $variant->product_group_option_id,
                'name' => $variant->name,
                'option_name' => $variant->groupOption?->name,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $unitPrice * $quantity,
            ];
        })->values()->all();
        $total = collect($orderItems)->sum('subtotal');
        $optionIds = collect($orderItems)->pluck('option_id')->unique()->values();
        $optionNames = collect($orderItems)->pluck('option_name')->unique()->values();
        $variantName = count($orderItems) === 1
            ? $orderItems[0]['name']
            : count($orderItems) . ' item dalam pesanan';
        $order = Order::query()->create([
            'order_number' => 'VL' . now()->format('ymd') . Str::upper(Str::random(8)),
            'product_id' => $product->id,
            'product_group_id' => $group->id,
            'product_group_option_id' => $optionIds->count() === 1 ? $firstVariant->product_group_option_id : null,
            'product_variant_id' => $firstVariant->id,
            'product_name' => $product->name,
            'group_name' => $group->name,
            'option_name' => $optionNames->count() === 1 ? $optionNames->first() : null,
            'variant_name' => $variantName,
            'items' => $orderItems,
            'customer_data' => $customerData,
            'total' => $total,
            'payment_method' => $base['payment_method'],
            'status' => 'pending_payment',
        ]);

        try {
            $recipients = User::query()
                ->where('role', 'admin')
                ->where('is_active', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($recipients !== []) {
                $expiresAt = now()->addDays(14);
                $completedUrl = URL::temporarySignedRoute('orders.email-action.confirm', $expiresAt, [
                    'order' => $order->id,
                    'status' => 'completed',
                ]);
                $cancelledUrl = URL::temporarySignedRoute('orders.email-action.confirm', $expiresAt, [
                    'order' => $order->id,
                    'status' => 'cancelled',
                ]);

                Mail::to($recipients)->send(new NewOrderAdminNotification($order, $completedUrl, $cancelledUrl));
            }
        } catch (Throwable $exception) {
            Log::error('Gagal mengirim email notifikasi pesanan baru.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'exception' => $exception->getMessage(),
            ]);
        }

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
        foreach ($order->items ?: [[
            'name' => $order->variant_name,
            'option_name' => $order->option_name,
            'quantity' => 1,
            'unit_price' => (float) $order->total,
            'subtotal' => (float) $order->total,
        ]] as $item) {
            $itemLabel = ($item['option_name'] ? $item['option_name'] . ' - ' : '') . $item['name'];
            $lines[] = "୨୧ {$itemLabel} × {$item['quantity']} — Rp" . number_format((float) $item['subtotal'], 0, ',', '.');
        }
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
