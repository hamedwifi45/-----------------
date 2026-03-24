<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;
    protected $fillable = [
        'contract_id',
        'reviewer_id',
        'reviewed_id',
        'type',
        'rating',
        'communication_rating',
        'quality_rating',
        'deadline_rating',
        'budget_rating',
        'comment',
        'private_feedback',
        'is_visible',
        'is_verified',
        'helpful_count',
        'helpful_users',
    ];

    protected $casts = [
        'rating' => 'integer',
        'communication_rating' => 'integer',
        'quality_rating' => 'integer',
        'deadline_rating' => 'integer',
        'budget_rating' => 'integer',
        'is_visible' => 'boolean',
        'is_verified' => 'boolean',
        'helpful_count' => 'integer',
        'helpful_users' => 'array',
    ];

    protected $hidden = [
        'private_feedback',
    ];

    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewed(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->communication_rating,
            $this->quality_rating,
            $this->deadline_rating,
            $this->budget_rating,
        ]);

        return $ratings ? array_sum($ratings) / count($ratings) : $this->rating;
    }
}
