<?php

namespace App\Http\Controllers\Api\Admin\Coupon;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        return response()->json(
            Coupon::latest()->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:percentage,flat',
            'value' => 'required|numeric|min:1',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string'
        ]);

        $coupon = Coupon::create($data);

        return response()->json([
            'message' => 'Coupon created successfully',
            'data' => $coupon
        ]);
    }

    public function show($id)
    {
        return response()->json(
            Coupon::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $coupon->update($request->all());

        return response()->json([
            'message' => 'Coupon updated successfully',
            'data' => $coupon
        ]);
    }

    public function destroy($id)
    {
        Coupon::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Coupon deleted successfully'
        ]);
    }
}

