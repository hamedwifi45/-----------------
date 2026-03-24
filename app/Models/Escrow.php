<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escrow extends Model
{
    /** @use HasFactory<\Database\Factories\EscrowFactory> */
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     * - contract_id: معرف العقد المرتبط بحساب الضمان
     * - employer_id: معرف أصحاب العمل
     * - freelancer_id: معرف المستقلين
     * - wallet_id: معرف المحفظة
     * - amount: المبلغ الإجمالي لحساب الضمان
     * - commission: الرسوم المطبقة على حساب الضمان
     * - net_amount: المبلغ الصافي بعد خصم الرسوم
     * - refunded_amount: المبلغ الذي تم استرداده (إذا تم استرداد جزء أو كامل المبلغ)
     * - status: حالة حساب الضمان (مثل "held"، "released"، "disputed")
     * - funded_at: تاريخ ووقت تمويل حساب الضمان
     * - released_at: تاريخ ووقت إصدار الأموال من حساب الضمان إلى المستقل
     * - refunded_at: تاريخ ووقت استرداد الأموال إلى صاحب العمل (إذا تم استردادها)
     * - expires_at: تاريخ ووقت انتهاء صلاحية حساب الضمان (إذا لم يتم إصدار أو استرداد الأموال قبل هذا التاريخ)
     * - released_by: معرف المستخدم الذي قام بإصدار الأموال (يمكن أن يكون مسؤول أو نظام تلقائي)
     * - refunded_by: معرف المستخدم الذي قام باسترداد الأموال (يمكن أن يكون مسؤول أو نظام تلقائي)
     * - disputed_by: معرف المستخدم الذي قام بفتح نزاع على حساب الضمان (يمكن أن يكون صاحب العمل أو المستقل)
     * - release_note: ملاحظات أو سبب إصدار الأموال من حساب الضمان
     * - refund_note: ملاحظات أو سبب استرداد الأموال إلى صاحب العمل
     * - admin_notes: ملاحظات إدارية خاصة بحساب الضمان (غير مرئية للمستخدمين العاديين)
     * - reference_id: معرف مرجعي داخلي لربط حساب الضمان بأحداث أخرى أو سجلات
     * - metadata: بيانات إضافية مخزنة كـ JSON (مثل تفاصيل العقد، معلومات الطرف الآخر، إلخ)
     */
    protected $fillable = [
        'contract_id',
        'employer_id',
        'freelancer_id',
        'wallet_id',
        'amount',
        'commission',
        'net_amount',
        'refunded_amount',
        'status',
        'funded_at',
        'released_at',
        'refunded_at',
        'expires_at',
        'released_by',
        'refunded_by',
        'disputed_by',
        'release_note',
        'refund_note',
        'admin_notes',
        'reference_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'funded_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'admin_notes',
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

    public function wallet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function releaser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public function refunder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    public function disputer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'disputed_by');
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scopes
     */
    public function scopeHeld($query)
    {
        return $query->where('status', 'held');
    }

    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    public function scopeDisputed($query)
    {
        return $query->where('status', 'disputed');
    }

    public function isHeld(): bool
    {
        return $this->status === 'held';
    }

    public function isReleased(): bool
    {
        return $this->status === 'released';
    }

    public function isDisputed(): bool
    {
        return $this->status === 'disputed';
    }
}
