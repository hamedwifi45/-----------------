<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;
     /**
     * ⚠️ لا تسمح بالتعديل بعد الإنشاء
     */
     /**
      * المعاملات المالية (مثل الإيداع، السحب، التحويل) المرتبطة بمحفظة المستخدم. كل معاملة تسجل تفاصيل مثل النوع (إيداع/سحب/تحويل)، الاتجاه (دائن/مدين)، المبلغ، الرسوم، الرصيد قبل وبعد المعاملة، الحالة (معلق/معالج/فاشل)، وأي ملاحظات أو بيانات إضافية. هذا النموذج يساعد في تتبع كل حركة مالية بدقة وربطها بالمستخدم والمحفظة والعقود أو المشاريع ذات الصلة.
      * - user_id: معرف المستخدم الذي قام بالمعاملة
      * - wallet_id: معرف المحفظة المرتبطة بالمعاملة
      * - contract_id: (اختياري) معرف العقد المرتبط إذا كانت المعاملة ناتجة عن عقد معين
      * - project_id: (اختياري) معرف المشروع المرتبط إذا كانت المعاملة ناتجة عن مشروع معين
      * - escrow_id: (اختياري) معرف حساب الضمان إذا كانت المعاملة مرتبطة بضمان
      * - withdrawal_request_id: (اختياري) معرف طلب السحب إذا كانت المعاملة ناتجة عن طلب سحب
      * - type: نوع المعاملة (إيداع، سحب    ، تحويل)
      * - direction: اتجاه المعاملة (دائن أو مدين)
      * - amount: المبلغ الإجمالي للمعاملة
      * - fee: الرسوم المطبقة على المعاملة
      * - net_amount: المبلغ الصافي بعد خصم الرسوم
      * - balance_before: رصيد المحفظة قبل المعاملة
      * - balance_after: رصيد المحفظة بعد المعاملة
      * - reference_id: معرف مرجعي داخلي لربط المعاملة بأحداث أخرى
      * - external_reference: (اختياري) معرف مرجعي خارجي من بوابات الدفع أو الأنظمة الأخرى
      * - payment_method: طريقة الدفع المستخدمة (مثل بطاقة ائتمان، PayPal، تحويل بنكي)
      * - payment_gateway: بوابة الدفع    
      * -description : الوصف النصي للمعاملة
      * -metadata : بيانات إضافية مخزنة كـ JSON (مثل تفاصيل الدفع، معلومات الجهة المستلمة، إلخ)
      * -status : حالة المعاملة (معلق، معالج، فاشل)
      * -processed_at : تاريخ ووقت معالجة المعاملة
      * - failed_at : تاريخ ووقت فشل المعاملة (إذا فشلت)
      * -failure_reason : سبب فشل المعاملة (إذا فشلت)
      * -ip_address : عنوان IP الذي تم منه تنفيذ المعاملة
      * -user_agent : معلومات عن جهاز المستخدم أو المتصفح الذي تم منه تنفيذ المعاملة
      * -processed_by : معرف المستخدم الذي قام بمعالجة المعاملة (مثل مسؤول أو نظام تلقائي)
      * -is_locked : علامة لتحديد ما إذا كانت المعاملة مقفلة لمنع التعديلات بعد الإنشاء
      */
     protected $fillable = [
        'user_id',
        'wallet_id',
        'contract_id',
        'project_id',
        'escrow_id',
        'withdrawal_request_id',
        'type',
        'direction',
        'amount',
        'fee',
        'net_amount',
        'balance_before',
        'balance_after',
        'reference_id',
        'external_reference',
        'payment_method',
        'payment_gateway',
        'description',
        'metadata',
        'status',
        'processed_at',
        'failed_at',
        'failure_reason',
        'ip_address',
        'user_agent',
        'processed_by',
        'is_locked',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'is_locked' => 'boolean',
    ];

    
    protected static function boot()
    {
        // منع التعديل على المعاملة بعد الإنشاء إذا كانت مقفلة
        parent::boot();

        static::updating(function ($transaction) {
            if ($transaction->is_locked && $transaction->isDirty()) {
                throw new \Exception('Cannot modify locked transaction');
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
    // العلاقات الاختيارية للعقود والمشاريع والضمانات وطلبات السحب
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    // علاقة اختيارية بحساب الضمان إذا كانت المعاملة مرتبطة بضمان
    public function escrow()
    {
        return $this->belongsTo(Escrow::class);
    }
    // علاقة اختيارية بطلب السحب إذا كانت المعاملة ناتجة عن طلب سحب
    public function withdrawalRequest()
    {
        return $this->belongsTo(WithdrawalRequest::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scopes
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    public function scopeCredit($query)
    {
        return $query->where('direction', 'credit');
    }
    // نطاق لتصفية المعاملات ذات الاتجاه "مدين"
    public function scopeDebit($query)
    {
        return $query->where('direction', 'debit');
    }
}
