<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\OtpRequest;
use App\Http\Requests\User\Auth\OtpVerifyRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Models\User;
use App\Models\LoginOtp;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['name'] = $data['first_name'].' '.$data['last_name'];

        $user = User::create($data);

        $tokenName = $request->string('device_name')->toString() ?: 'api';
        $plainTextToken = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'dob' => optional($user->dob)->toDateString(),
                    'phone' => $user->phone,
                    'status' => $user->status,
                ],
                'token' => $plainTextToken,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('email', $request->validated('email'))
            ->first();

        if (! $user || $user->status !== 'active' || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json([
                'message' => $user && $user->status !== 'active'
                    ? 'Account is inactive.'
                    : 'Invalid credentials.',
            ], $user && $user->status !== 'active' ? 403 : 401);
        }

        $tokenName = $request->string('device_name')->toString() ?: 'api';
        $plainTextToken = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'dob' => optional($user->dob)->toDateString(),
                    'phone' => $user->phone,
                    'status' => $user->status,
                ],
                'token' => $plainTextToken,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    public function logout(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = request()->user();

        $user->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /**
     * Request OTP login code (sent to email in real system).
     */
    public function requestOtp(OtpRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || $user->status !== 'active') {
            return response()->json([
                'message' => 'If this email exists and is active, an OTP has been sent.',
            ], 200);
        }

        $code = (string) random_int(100000, 999999);

        LoginOtp::create([
            'user_id' => $user->id,
            'admin_id' => null,
            'purpose' => 'user_login',
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        // TODO: In production, send OTP via mail / SMS.

        $response = [
            'message' => 'OTP generated successfully.',
        ];

        // For local / testing, return OTP so it can be used easily.
        if (app()->isLocal()) {
            $response['debug_otp'] = $code;
        }

        return response()->json($response, 200);
    }

    /**
     * Verify OTP and issue Sanctum token.
     */
    public function verifyOtp(OtpVerifyRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || $user->status !== 'active') {
            return response()->json([
                'message' => 'Invalid OTP or email.',
            ], 422);
        }

        $otp = LoginOtp::query()
            ->where('user_id', $user->id)
            ->where('purpose', 'user_login')
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

        $tokenName = $request->string('device_name')->toString() ?: 'api-otp';
        $plainTextToken = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully via OTP.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'dob' => optional($user->dob)->toDateString(),
                    'phone' => $user->phone,
                    'status' => $user->status,
                ],
                'token' => $plainTextToken,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }
}

