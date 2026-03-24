<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    /** @use HasFactory<\Database\Factories\BidFactory> */
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     * - project_id: معرف المشروع الذي تم تقديم العرض له
     * - freelancer_id: معرف المستقل الذي قدم العرض
     * - amount: المبلغ المقترح للعمل على المشروع
     * - duration_days: عدد الأيام المتوقعة لإكمال المشروع وفقًا للعرض
     * - proposal: نص العرض أو الاقتراح الذي قدمه المستقل لصاحب العمل
     * - cover_letter: رسالة تغطية إضافية من المستقل (اختياري)
     * - status: حالة العرض (مثل "pending"، "accepted"، "rejected")
     * - rating: تقييم صاحب العمل للمستقل بعد إكمال المشروع (1-5 نجوم)
     * - employer_feedback: ملاحظات صاحب العمل حول أداء المستقل في المشروع
     * - accepted_at: تاريخ ووقت قبول العرض من قبل صاحب العمل
     * - rejected_at: تاريخ ووقت رفض العرض من قبل صاحب العمل
     * - expires_at: تاريخ ووقت انتهاء صلاحية العرض (بعد هذا التاريخ، لا يمكن قبول أو رفض العرض)
     */
    protected $fillable = [
        'project_id',
        'freelancer_id',
        'amount',
        'duration_days',
        'proposal',
        'cover_letter',
        'status',
        'rating',
        'employer_feedback',
        'accepted_at',
        'rejected_at',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'duration_days' => 'integer',
        'rating' => 'integer',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function freelancer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function contract(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }
}
