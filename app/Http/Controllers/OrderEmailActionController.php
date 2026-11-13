<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderEmailActionController extends Controller
{
    public function confirm(Order $order, string $status): View
    {
        abort_unless(in_array($status, ['completed', 'cancelled'], true), 404);

        return view('orders.email-action', [
            'order' => $order,
            'targetStatus' => $status,
            'alreadyProcessed' => $order->status !== 'pending_payment',
            'updated' => false,
        ]);
    }

    public function update(Order $order, string $status): View
    {
        abort_unless(in_array($status, ['completed', 'cancelled'], true), 404);

        $updated = DB::transaction(function () use ($order, $status) {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($lockedOrder->status !== 'pending_payment') {
                return false;
            }

            $attributes = ['status' => $status];
            if ($status === 'completed') {
                $attributes['completed_at'] = now();
            }
            $lockedOrder->update($attributes);

            return true;
        });

        return view('orders.email-action', [
            'order' => $order->fresh(),
            'targetStatus' => $status,
            'alreadyProcessed' => ! $updated,
            'updated' => $updated,
        ]);
    }
}
