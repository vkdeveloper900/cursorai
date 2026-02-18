<?php

namespace App\Http\Middleware\Permissions;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = $request->user();

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated user.'
            ], 401);
        }

        if (!$admin->role) {
            return response()->json([
                'message' => 'Role not assigned to this user.'
            ], 403);
        }

        // Super admin bypass
//        if (in_array($admin->role->slug, ['administration', 'super_admin'])) {
//            return $next($request);
//        }

        $admin->loadMissing('role.permissions');

        $requiredPermissions = explode(',', $permission);

        $hasPermission = $admin->role->permissions
            ->pluck('key')
            ->intersect($requiredPermissions)
            ->isNotEmpty();

        if (!$hasPermission) {
            return response()->json([
                'message' => 'Permission denied.',
                'required_permission' => $requiredPermissions
            ], 403);
        }

        return $next($request);
    }

}
