<?php

namespace App\Http\Controllers\Api\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Test;
use App\Models\Question;
use App\Models\Package;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();

        $totalOrders = Order::count();
        $paidOrders = Order::where('status', 'paid')->count();
        $failedOrders = Order::where('status', 'failed')->count();

        $totalCollection = Payment::where('status', 'success')
            ->sum('amount');

        $totalTests = Test::count();
        $totalQuestions = Question::count();

        $activePackage = Package::where('status', 'active')
            ->select('id', 'name', 'price')
            ->first();

        return response()->json([
            'total_users' => $totalUsers,
            'total_orders' => $totalOrders,
            'paid_orders' => $paidOrders,
            'failed_orders' => $failedOrders,
            'total_collection' => (float) $totalCollection,
            'total_tests' => $totalTests,
            'total_questions' => $totalQuestions,
            'active_package' => $activePackage
        ]);
    }
}


