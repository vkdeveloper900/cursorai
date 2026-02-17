<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    /**
     * Generic social login / signup endpoint.
     *
     * In a real system you would verify the provider token (id_token / access_token)
     * with Google / Apple / Meta before trusting the payload. Here we keep it
     * simple for a test platform and assume the client sends a valid payload.
     */
    public function handle(string $provider, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider_user_id' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $provider = strtolower($provider);

        if (! in_array($provider, ['google', 'apple', 'meta'], true)) {
            return response()->json([
                'message' => 'Unsupported provider.',
            ], 422);
        }

        $user = User::query()
            ->where('email', $validated['email'])
            ->first();

        if (! $user) {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $validated['first_name'].' '.$validated['last_name'],
                'email' => $validated['email'],
                'status' => 'active',
                'social_provider' => $provider,
                'social_id' => $validated['provider_user_id'],
                // No password required for pure social accounts
                'password' => 'social-'.bin2hex(random_bytes(5)),
            ]);
        } else {
            $user->forceFill([
                'social_provider' => $provider,
                'social_id' => $validated['provider_user_id'],
            ])->save();
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Account is inactive.',
            ], 403);
        }

        $tokenName = $request->string('device_name')->toString() ?: $provider.'-api';
        $plainTextToken = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully via '.$provider.'.',
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

