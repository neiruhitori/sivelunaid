<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrdersController extends Controller
{
    public function index()
    {
        return view('admin.orders.index', ['statuses' => Order::STATUSES]);
    }

    public function data()
    {
        return DataTables::eloquent(Order::query()->select([
            'id', 'order_number', 'created_at', 'product_name', 'group_name', 'option_name',
            'variant_name', 'total', 'payment_method', 'status',
        ]))
            ->addIndexColumn()
            ->editColumn('total', fn (Order $order) => 'Rp' . number_format((float) $order->total, 0, ',', '.'))
            ->editColumn('created_at', fn (Order $order) => $order->created_at?->format('d/m/Y H:i'))
            ->addColumn('status_control', fn (Order $order) => $this->statusControl($order->status))
            ->addColumn('actions', fn (Order $order) => '<button type="button" class="btn btn-outline-info btn-sm" data-order-detail="' . $order->id . '"><i class="fas fa-eye mr-1"></i>Detail</button>')
            ->rawColumns(['status_control', 'actions'])
            ->make(true);
    }

    public function pending()
    {
        $orders = Order::query()
            ->where('status', 'pending_payment')
            ->latest()
            ->paginate(10, [
                'id', 'order_number', 'created_at', 'product_name', 'group_name', 'option_name',
                'variant_name', 'total', 'payment_method', 'customer_data',
            ]);

        return response()->json([
            'orders' => $orders->items(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'total' => $orders->total(),
        ]);
    }

    public function show(Order $order)
    {
        return response()->json($order);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(Order::STATUSES))],
        ]);

        $attributes = ['status' => $validated['status']];
        if ($validated['status'] === 'completed' && ! $order->completed_at) {
            $attributes['completed_at'] = now();
        }
        $order->update($attributes);

        return response()->json(['success' => true, 'message' => 'Status pesanan diperbarui.']);
    }

    private function statusControl(string $status): string
    {
        $options = '';
        foreach (Order::STATUSES as $value => $label) {
            $selected = $status === $value ? ' selected' : '';
            $options .= '<option value="' . e($value) . '"' . $selected . '>' . e($label) . '</option>';
        }

        return '<select class="form-control form-control-sm order-status-select" data-order-status="' . e($status) . '">' . $options . '</select>';
    }
}
