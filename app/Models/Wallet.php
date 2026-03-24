<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\WalletFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'balance',
        'pending_balance',
        'reserved_balance',
        'total_deposited',
        'total_withdrawn',
        'total_earned',
        'total_spent',
        'currency',
        'is_active',
        'can_withdraw',
        'withdrawal_limit_daily',
        'withdrawal_limit_monthly',
        'admin_notes',
        'frozen_at',
        'freeze_reason',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'reserved_balance' => 'decimal:2',
        'total_deposited' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'withdrawal_limit_daily' => 'decimal:2',
        'withdrawal_limit_monthly' => 'decimal:2',
        'is_active' => 'boolean',
        'can_withdraw' => 'boolean',
        'frozen_at' => 'datetime',
    ];

    protected $hidden = [
        'admin_notes',
        'freeze_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    // المعاملات المالية المتعلقة بهذا المحفظة (سواء إيداع أو سحب أو تحويل)
    public function escrows()
    {
        return $this->hasMany(Escrow::class);
    }
    // طلبات السحب المرتبطة بهذا المحفظة
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }
    // سجلات الدفع المرتبطة بهذا المحفظة (مثل دفعات العملاء أو مدفوعات للمستقلين)
    public function paymentLogs()
    {
        return $this->hasMany(PaymentLog::class);
    }

    /**
     * ⚠️ دوال مالية آمنة
     */
    public function getTotalBalanceAttribute(): string
    {   
        // إجمالي الرصيد المتاح (الرصيد الحالي + الرصيد المحجوز)
        return bcadd($this->balance, $this->reserved_balance, 2);
    }

    public function isFrozen(): bool
    {
        return $this->frozen_at !== null;
    }
    // تحقق إذا كان المستخدم يمكنه سحب مبلغ معين (مع الأخذ في الاعتبار الرصيد المتاح والقيود)
    public function canWithdrawAmount(float $amount): bool
    {
        if (!$this->can_withdraw || $this->isFrozen()) {
            return false;
        }
        return $this->balance >= $amount;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCanWithdraw($query)
    {
        return $query->where('can_withdraw', true)
            ->whereNull('frozen_at');
    }
}
