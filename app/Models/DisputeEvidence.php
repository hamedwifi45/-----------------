<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisputeEvidence extends Model
{
    /** @use HasFactory<\Database\Factories\DisputeEvidenceFactory> */
    use HasFactory;
     protected $fillable = [
        'dispute_id',
        'submitted_by',
        'type',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'message_id',
        'deliverable_id',
        'is_verified',
        'submitted_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_verified' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function dispute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }

    public function submitter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function message(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function deliverable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Deliverable::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}
