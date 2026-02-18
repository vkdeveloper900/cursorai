<?php

namespace App\Http\Controllers\Api\Admin\RolePermission;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolePermissionController extends Controller
{
    /**
     * List all roles with permissions
     */
    public function roles()
    {
        $roles = Role::with('permissions:id,name,key,group')
            ->select('id', 'name', 'slug', 'description')
            ->get();

        return response()->json($roles);
    }

    /**
     * Create new role
     */
    public function createRole(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:roles,slug',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($data);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);
    }

    /**
     * Update role
     */
    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => [
                'required',
                'string',
                Rule::unique('roles', 'slug')->ignore($role->id),
            ],
            'description' => 'nullable|string',
        ]);

        $role->update($data);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    /**
     * Delete role
     */
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);

        if ($role->slug === 'super_admin') {
            return response()->json([
                'message' => 'Super Admin role cannot be deleted'
            ], 403);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Assign / Sync permissions to role
     */
    public function assignPermissions(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        if ($role->slug === 'super_admin') {
            return response()->json([
                'message' => 'Super Admin has all permissions by default'
            ]);
        }

        $data = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($data['permission_ids']);

        return response()->json([
            'message' => 'Permissions assigned successfully'
        ]);
    }

    /**
     * Get all permissions (grouped)
     */
    public function permissions()
    {
        $permissions = Permission::select(
            'id', 'name', 'key', 'group', 'description'
        )
            ->orderBy('group')
            ->get()
            ->groupBy('group');

        return response()->json($permissions);
    }

    /**
     * Get single role with permission IDs (for edit screen)
     */
    public function roleDetails($id)
    {
        $role = Role::with('permissions:id')
            ->select('id', 'name', 'slug', 'description')
            ->findOrFail($id);

        return response()->json([
            'role' => $role,
            'permission_ids' => $role->permissions->pluck('id')
        ]);
    }
}
