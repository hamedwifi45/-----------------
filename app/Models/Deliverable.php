<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deliverable extends Model
{
    /** @use HasFactory<\Database\Factories\DeliverableFactory> */
    use HasFactory;
    protected $fillable = [
        'contract_id',
        'freelancer_id',
        'title',
        'description',
        'version',
        'is_final',
        'status',
        'revision_number',
        'client_feedback',
        'freelancer_response',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'revision_requested_at',
        'reviewed_by',
    ];

    protected $casts = [
        'version' => 'integer',
        'is_final' => 'boolean',
        'revision_number' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'revision_requested_at' => 'datetime',
    ];

    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function freelancer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRevisionRequested($query)
    {
        return $query->where('status', 'revision_requested');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isFinal(): bool
    {
        return $this->is_final === true;
    }
}
