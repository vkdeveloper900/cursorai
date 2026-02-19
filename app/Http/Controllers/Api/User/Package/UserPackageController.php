<?php

namespace App\Http\Controllers\Api\User\Package;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\UserPackage;
use Illuminate\Http\Request;

class UserPackageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $packages = Package::with(['tests'])
            ->where('status', 1)
            ->get()
            ->map(function ($package) use ($user) {

                $activePurchase = UserPackage::where('user_id', $user->id)
                    ->where('package_id', $package->id)
                    ->where('status', 'active')
                    ->where('expiry_date', '>', now())
                    ->first();

                return [
                    'id' => $package->id,
                    'title' => $package->title,
                    'description' => $package->description,
                    'price' => $package->price,
                    'validity_days' => $package->validity_days,
                    'total_tests' => $package->tests->count(),
                    'is_purchased' => $activePurchase ? true : false,
                    'expiry_date' => $activePurchase?->expiry_date,
                    'tests' => $package->tests->map(function ($test) {
                        return [
                            'id' => $test->id,
                            'title' => $test->title,
                            'difficulty' => $test->difficulty,
                            'total_time' => $test->total_time,
                        ];
                    }),
                ];
            });

        return response()->json([
            'data' => $packages
        ]);
    }

    public function show($id, Request $request)
    {
        $package = Package::with('tests')
            ->where('status', 1)
            ->findOrFail($id);

        return response()->json([
            'data' => $package
        ]);
    }
}
