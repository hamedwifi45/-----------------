<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permission')->withTimestamps();
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles')->withTimestamps();
    }
    public function hasPermission($permissionName)
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }
    public function givePermission(string $permissionName): void
    {
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }
    public function revokePermission(string $permissionName): void
    {
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
    }
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }
}
