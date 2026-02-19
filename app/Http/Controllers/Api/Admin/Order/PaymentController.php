<?php

namespace App\Http\Controllers\Api\Admin\Order;

namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        return response()->json(
            Payment::with('order')->latest()->paginate(10)
        );
    }

    public function show($id)
    {
        return response()->json(
            Payment::with('order')->findOrFail($id)
        );
    }

    public function orderPayments($orderId)
    {
        return response()->json(
            Payment::where('order_id', $orderId)->latest()->get()
        );
    }
}
