<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    /** @use HasFactory<\Database\Factories\ProfileFactory> */
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'display_name',
        'bio',
        'headline',
        'country',
        'city',
        'phone_public',
        'social_links',
        'hourly_rate',
        'availability',
        'job_success_score',
        'completed_jobs',
        'in_progress_jobs',
        'total_earnings',
        'total_spent',
        'profile_image',
        'cover_image',
        'is_verified',
        'verified_at',
        'verification_documents',
        'is_complete',
        'is_public',
    ];
    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'job_success_score' => 'integer',
        'completed_jobs' => 'integer',
        'in_progress_jobs' => 'integer',
        'total_earnings' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'verification_documents' => 'array',
        'social_links' => 'array',
        'is_complete' => 'boolean',
        'is_public' => 'boolean',
    ];
    protected $hidden = [
        'verification_documents'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function Skills()
    {
        return $this->belongsToMany(Skill::class, 'profile_skill')->withPivot('proficiency_level','years_of_experience', 'is_primary')
        ->withTimestamps();
    }
    public function portfoliosItems()
    {
        return $this->hasMany(PortfolioItem::class);
    }
    public function getFullNameAttribute(): string
    {
        return trim( "{$this->first_name} {$this->last_name}" );
    }
    public function scopeVerified( $query )
    {
        return $query->where('is_verified', true);
    }
    public function scopeAvailable( $query )
    {
        return $query->where('availability', 'available');
    }
    public function scopePublic($query){
        return $query->where('is_public', true);
    }
    
}
