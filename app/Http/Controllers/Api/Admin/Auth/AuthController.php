<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\RegisterRequest;
use App\Http\Requests\Admin\Auth\TwoFactorRequest;
use App\Http\Requests\Admin\Auth\TwoFactorVerifyRequest;
use App\Models\Admin;
use App\Models\LoginOtp;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['name'] = $data['first_name'].' '.$data['last_name'];

        $admin = Admin::create($data);

        $tokenName = $request->string('device_name')->toString() ?: 'admin-api';
        $plainTextToken = $admin->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Admin registered successfully.',
            'data' => [
                'admin' => [
                    'id' => $admin->id,
                    'first_name' => $admin->first_name,
                    'last_name' => $admin->last_name,
                    'email' => $admin->email,
                    'mobile' => $admin->mobile,
                    'status' => $admin->status,
                ],
                'token' => $plainTextToken,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $admin = Admin::query()
            ->where('email', $request->validated('email'))
            ->first();

        if (! $admin || $admin->status !== 'active' || ! Hash::check($request->validated('password'), $admin->password)) {
            return response()->json([
                'message' => $admin && $admin->status !== 'active'
                    ? 'Account is inactive.'
                    : 'Invalid credentials.',
            ], $admin && $admin->status !== 'active' ? 403 : 401);
        }

        $tokenName = $request->string('device_name')->toString() ?: 'admin-api';
        $plainTextToken = $admin->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Admin logged in successfully.',
            'data' => [
                'admin' => [
                    'id' => $admin->id,
                    'first_name' => $admin->first_name,
                    'last_name' => $admin->last_name,
                    'email' => $admin->email,
                    'mobile' => $admin->mobile,
                    'status' => $admin->status,
                ],
                'token' => $plainTextToken,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    public function logout(): JsonResponse
    {
        /** @var \App\Models\Admin $admin */
        $admin = request()->user();

        $admin->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Admin logged out successfully.',
        ], 200);
    }

    /**
     * Start 2-step login: verify password, then send OTP.
     */
    public function startTwoFactor(TwoFactorRequest $request): JsonResponse
    {
        $admin = Admin::where('email', $request->validated('email'))->first();

        if (! $admin || $admin->status !== 'active') {
            return response()->json([
                'message' => 'If this admin exists and is active, an OTP has been sent.',
            ], 200);
        }

        $code = (string) random_int(100000, 999999);

        LoginOtp::create([
            'user_id' => null,
            'admin_id' => $admin->id,
            'purpose' => 'admin_two_factor',
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = [
            'message' => 'Two-factor code generated successfully.',
        ];

        if (app()->isLocal()) {
            $response['debug_otp'] = $code;
        }

        return response()->json($response, 200);
    }

    /**
     * Verify 2-step OTP for admin and issue token.
     */
    public function verifyTwoFactor(TwoFactorVerifyRequest $request): JsonResponse
    {
        $admin = Admin::where('email', $request->validated('email'))->first();

        if (! $admin || $admin->status !== 'active') {
            return response()->json([
                'message' => 'Invalid OTP or email.',
            ], 422);
        }

        $otp = LoginOtp::query()
            ->where('admin_id', $admin->id)
            ->where('purpose', 'admin_two_factor')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp || $otp->code !== $request->validated('code')) {
            return response()->json([
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $otp->forceFill(['used_at' => now()])->save();

        $tokenName = $request->string('device_name')->toString() ?: 'admin-2fa';
        $plainTextToken = $admin->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Admin logged in successfully with 2-step verification.',
            'data' => [
                'admin' => [
                    'id' => $admin->id,
                    'first_name' => $admin->first_name,
                    'last_name' => $admin->last_name,
                    'email' => $admin->email,
                    'mobile' => $admin->mobile,
                    'status' => $admin->status,
                ],
                'token' => $plainTextToken,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }
}

