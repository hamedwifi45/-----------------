<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory;
    protected $guarded = [];

    // لماذا اخترنا UUID كمعرف بدلاً من الأعداد المتزايدة؟
    // 1. الأمان: UUIDs أصعب في التخمين من الأعداد المتزايدة، مما يجعل من الصعب على المهاجمين استهداف إشعارات معينة.
    // 2. التوزيع: في بيئة موزعة أو عند استخدام قواعد بيانات متعددة، يمكن إنشاء UUIDs بشكل مستقل دون خطر التعارض، بينما الأعداد المتزايدة قد تتعارض إذا تم إنشاؤها في قواعد بيانات مختلفة.
    // 3. الخصوصية: UUIDs لا تكشف عن عدد السجلات أو ترتيب الإنشاء، مما يوفر طبقة إضافية من الخصوصية.
    // 4. التوافق: UUIDs معيارية وتستخدم على نطاق واسع في العديد من الأنظمة والتطبيقات
    public $incrementing = false;

    protected $keyType = 'string';
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * دوال مساعدة
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * الحصول على بيانات محددة من JSON
     */
    public function getTitleAttribute(): ?string
    {
        return $this->data['title'] ?? null;
    }

    public function getMessageAttribute(): ?string
    {
        return $this->data['message'] ?? null;
    }

    public function getUrlAttribute(): ?string
    {
        return $this->data['url'] ?? null;
    }

    public function getIconAttribute(): ?string
    {
        return $this->data['icon'] ?? null;
    }

    public function getActionTypeAttribute(): ?string
    {
        return $this->data['action_type'] ?? null;
    }

    public function getActionIdAttribute(): ?string
    {
        return $this->data['action_id'] ?? null;
    }
}
