<?php

namespace App\Http\Controllers\Api\Admin\Order;

namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'package', 'coupon'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with([
            'user',
            'package',
            'coupon',
            'payments'
        ])->findOrFail($id);

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:created,paid,failed,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated',
            'data' => $order
        ]);
    }

    public function stats()
    {
        return response()->json([
            'total_orders' => Order::count(),
            'paid_orders' => Order::where('status', 'paid')->count(),
            'failed_orders' => Order::where('status', 'failed')->count(),
            'total_collection' => Payment::where('status', 'success')->sum('amount'),
        ]);
    }
}
