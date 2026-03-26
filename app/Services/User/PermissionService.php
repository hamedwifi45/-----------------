<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PermissionService
{
    /**
     * إنشاء دور جديد
     */
    public function createRole(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'description' => $data['description'] ?? null,
            'is_system' => $data['is_system'] ?? false,
        ]);
    }

    /**
     * إنشاء صلاحية جديدة
     */
    public function createPermission(array $data): Permission
    {
        return Permission::create([
            'name' => $data['name'],
            'group' => $data['group'] ?? null,
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * منح دور لمستخدم
     */
    public function assignRoleToUser(User $user, Role $role): void
    {
        DB::beginTransaction();
        
        try {
            // التحقق من عدم وجود الدور بالفعل
            if ($user->roles()->where('role_id', $role->id)->exists()) {
                throw new Exception('Role already assigned');
            }

            $user->roles()->attach($role->id);

            // إذا كان لديه دور، اجعله staff
            if ($user->roles()->count() > 0) {
                $user->update(['is_staff' => true]);
            }

            DB::commit();

            Log::info('Role assigned to user', [
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * سحب دور من مستخدم
     */
    public function removeRoleFromUser(User $user, Role $role): void
    {
        $user->roles()->detach($role->id);

        // إذا لم يعد لديه أدوار، أزل staff
        if ($user->roles()->count() === 0) {
            $user->update(['is_staff' => false]);
        }

        Log::info('Role removed from user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    /**
     * منح صلاحية لدور
     */
    public function assignPermissionToRole(Role $role, Permission $permission): void
    {
        if (!$role->permissions()->where('permission_id', $permission->id)->exists()) {
            $role->permissions()->attach($permission->id);
            
            Log::info('Permission assigned to role', [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        }
    }

    /**
     * سحب صلاحية من دور
     */
    public function removePermissionFromRole(Role $role, Permission $permission): void
    {
        $role->permissions()->detach($permission->id);

        Log::info('Permission removed from role', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);
    }

    /**
     * التحقق من أن المستخدم يملك صلاحية
     */
    public function userHasPermission(User $user, string $permissionName): bool
    {
        // المدير العام يملك كل الصلاحيات
        if ($user->role === 'admin') {
            return true;
        }

        foreach ($user->roles as $role) {
            if ($role->permissions()->where('name', $permissionName)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * الحصول على دور بالاسم
     */
    public function getRoleByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }

    /**
     * الحصول على صلاحية بالاسم
     */
    public function getPermissionByName(string $name): ?Permission
    {
        return Permission::where('name', $name)->first();
    }
}