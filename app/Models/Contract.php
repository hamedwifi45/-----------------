<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    /** @use HasFactory<\Database\Factories\ContractFactory> */
    use HasFactory;
    protected $fillable = [
        'project_id',
        'bid_id',
        'employer_id',
        'freelancer_id',
        'amount',
        'commission',
        'net_amount',
        'duration_days',
        'start_date',
        'due_date',
        'completed_at',
        'cancelled_at',
        'status',
        'deliverables_count',
        'revisions_allowed',
        'revisions_used',
        'cancellation_reason',
        'cancelled_by',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'duration_days' => 'integer',
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'deliverables_count' => 'integer',
        'revisions_allowed' => 'integer',
        'revisions_used' => 'integer',
    ];

    protected $hidden = [
        'admin_notes',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function escrow(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Escrow::class);
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverable::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDisputed($query)
    {
        return $query->where('status', 'disputed');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isDisputed(): bool
    {
        return $this->status === 'disputed';
    }

    public function canBeCompleted(): bool
    {
        return $this->isActive() && !$this->isDisputed();
    }
}
