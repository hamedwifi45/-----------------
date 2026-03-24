<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    
     /**
     * The attributes that are mass assignable.
     * - employer_id: معرف صاحب العمل الذي نشر المشروع
     * - title: عنوان المشروع
     * - slug: نسخة صديقة للرابط من العنوان (تستخدم في URL)
     * - description: وصف مفصل للمشروع ومتطلباته
     * - requirements: متطلبات المشروع (يمكن أن تكون نصًا أو JSON)
     * - budget_min: الحد الأدنى للميزانية للمشروع
     * - budget_max: الحد الأقصى للميزانية للمشروع
     * - budget_type: نوع الميزانية (مثل "fixed" أو "hourly")
     * - currency: العملة المستخدمة في الميزانية (مثل "USD" أو "EUR")
     * - duration_days: المدة المتوقعة لإكمال المشروع بالأيام
     * - deadline: الموعد النهائي لتقديم العروض أو إكمال المشروع
     * - status: حالة المشروع (مثل "open"، "in_progress"، "completed")
     * - proposals_count: عدد العروض المقدمة للمشروع
     * - is_featured: علامة لتحديد ما إذا كان المشروع مميزًا أم لا
     * - is_urgent: علامة لتحديد ما إذا كان المشروع عاجلاً أم لا
     * - is_hidden: علامة لتحديد ما إذا كان المشروع مخفيًا عن العرض العام أم لا
     * - published_at: تاريخ ووقت نشر المشروع
     * - expires_at: تاريخ ووقت انتهاء صلاحية المشروع (بعد هذا التاريخ، لا يمكن تقديم عروض جديدة)
     * - views_count: عدد مرات مشاهدة المشروع من قبل المستخدمين
     * - favorites_count: عدد مرات إضافة المشروع إلى المفضلة من قبل المستخدمين
     */
    protected $fillable = [
        'employer_id',
        'title',
        'slug',
        'description',
        'requirements',
        'budget_min',
        'budget_max',
        'budget_type',
        'currency',
        'duration_days',
        'deadline',
        'status',
        'proposals_count',
        'is_featured',
        'is_urgent',
        'is_hidden',
        'published_at',
        'expires_at',
        'views_count',
        'favorites_count',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'duration_days' => 'integer',
        'proposals_count' => 'integer',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'is_hidden' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'views_count' => 'integer',
        'favorites_count' => 'integer',
        'deadline' => 'datetime',
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_skills')
            ->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false)
            ->where('status', 'open');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function hasContract(): bool
    {
        return $this->contract()->exists();
    }
}
