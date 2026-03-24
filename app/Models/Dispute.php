<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    /** @use HasFactory<\Database\Factories\DisputeFactory> */
    use HasFactory;
    // معلومات عن النزاع:
    // - contract_id: معرف العقد المرتبط بالنزاع
    // - employer_id: معرف صاحب العمل الذي فتح النزاع       
    // - freelancer_id: معرف المستقل المتورط في النزاع
    // - opened_by: معرف المستخدم الذي فتح النزاع (يمكن أن يكون صاحب العمل أو   المستقل)
    // - assigned_admin: معرف المسؤول الذي تم تعيينه للنظر في النزاع
    // - title: عنوان مختصر للنزاع
    // - description: وصف مفصل للنزاع والمشكلة التي يواجهها الطرف   
    protected $fillable = [
        'contract_id',
        'employer_id',
        'freelancer_id',
        'opened_by',
        'assigned_admin',
        'title',
        'description',
        'reason',
        'status',
        'resolution',
        'refunded_amount',
        'paid_amount',
        'opened_at',
        'resolved_at',
        'closed_at',
        'admin_decision',
        'resolution_notes',
    ];

    protected $casts = [
        'refunded_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'opened_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function employer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function freelancer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function opener(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin');
    }

    public function evidence(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DisputeEvidence::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
