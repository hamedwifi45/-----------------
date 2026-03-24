<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    /** @use HasFactory<\Database\Factories\SkillFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'name_ar',
        'name_en',
        'category',
        'parent_skill_id',
        'is_active',
        'is_featured',
        'sort_order',
        'usage_count',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'usage_count' => 'integer',
    ];
    public function parent()
    {
        return $this->belongsTo(Skill::class, 'parent_skill_id');
    }
    public function children()
    {
        return $this->hasMany(Skill::class, 'parent_skill_id');
    }
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_skill')
        ->withPivot('proficiency_level', 'years_of_experience', 'is_primary')->withTimestamps();
    }
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_skill')->withTimestamps();
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
