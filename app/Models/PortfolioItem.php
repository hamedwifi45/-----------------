<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioItem extends Model
{
    /** @use HasFactory<\Database\Factories\PortfolioItemFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profile_id',
        'title',
        'slug',
        'description',
        'project_details',
        'category',
        'skills_used',
        'client_name',
        'is_confidential',
        'project_url',
        'demo_url',
        'is_published',
        'is_featured',
        'views_count',
        'likes_count',
        'sort_order',
    ];

    protected $casts = [
        'skills_used' => 'array',
        'is_confidential' => 'boolean',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PortfolioImage::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_confidential', false)
            ->where('is_published', true);
    }

    public function coverImage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PortfolioImage::class)->where('is_cover', true);
    }
}
