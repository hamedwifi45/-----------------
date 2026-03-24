<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'group',
    ];
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permission')->withTimestamps();
    }
    public function scopeGroup($query, string $group): void
    {
        $query->where('group', 'like', $group . ':%');
    }
}
