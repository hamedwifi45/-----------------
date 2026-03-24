<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    /** @use HasFactory<\Database\Factories\PortfolioImageFactory> */
    use HasFactory;
    protected $fillable = [
        'portfolio_item_id',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'width',
        'height',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_cover' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function portfolioItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PortfolioItem::class);
    }

    public function scopeCover($query)
    {
        return $query->where('is_cover', true);
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

}
