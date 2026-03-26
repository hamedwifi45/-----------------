<?php

namespace App\Services\Financial;

use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use App\Services\Financial\TransactionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WalletService
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * إنشاء محفظة جديدة للمستخدم
     */
    public function createWallet(User $user): Wallet
    {
        return Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'pending_balance' => 0,
            'reserved_balance' => 0,
            'currency' => 'SAR',
            'is_active' => true,
            'can_withdraw' => true,
        ]);
    }

    /**
     * الحصول على محفظة المستخدم أو إنشاؤها
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        return $user->wallet ?? $this->createWallet($user);
    }

    /**
     * التحقق من رصيد المحفظة
     */
    public function checkBalance(Wallet $wallet, float $amount): bool
    {
        return $wallet->balance >= $amount;
    }

    /**
     * التحقق من أن المحفظة نشطة وغير مجمدة
     */
    public function isWalletActive(Wallet $wallet): bool
    {
        return $wallet->is_active && !$wallet->isFrozen();
    }

    /**
     * إيداع أموال في المحفظة ⚠️
     */
    public function deposit(Wallet $wallet, float $amount, array $metadata = []): Transaction
    {
        DB::beginTransaction();
        
        try {
            // التحقق من صحة المبلغ
            if ($amount <= 0) {
                throw new Exception('Invalid deposit amount');
            }

            // التحقق من حالة المحفظة
            if (!$this->isWalletActive($wallet)) {
                throw new Exception('Wallet is not active');
            }

            // حفظ الرصيد قبل العملية
            $balanceBefore = $wallet->balance;

            // تحديث الرصيد
            $wallet->increment('balance', $amount);
            $wallet->increment('total_deposited', $amount);
            $wallet->update(['last_transaction_at' => now()]);

            // تسجيل المعاملة
            $transaction = $this->transactionService->create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'direction' => 'credit',
                'amount' => $amount,
                'fee' => 0,
                'net_amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
                'metadata' => $metadata,
                'processed_at' => now(),
            ]);

            DB::commit();

            Log::info('Wallet deposit successful', [
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ]);

            return $transaction;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Wallet deposit failed', [
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * سحب أموال من المحفظة ⚠️
     */
    public function withdraw(Wallet $wallet, float $amount, array $metadata = []): Transaction
    {
        DB::beginTransaction();
        
        try {
            // التحقق من صحة المبلغ
            if ($amount <= 0) {
                throw new Exception('Invalid withdrawal amount');
            }

            // التحقق من حالة المحفظة
            if (!$this->isWalletActive($wallet)) {
                throw new Exception('Wallet is not active');
            }

            // التحقق من صلاحية السحب
            if (!$wallet->can_withdraw) {
                throw new Exception('Withdrawals are not allowed for this wallet');
            }

            // التحقق من الرصيد الكافي
            if (!$this->checkBalance($wallet, $amount)) {
                throw new Exception('Insufficient balance');
            }

            // حفظ الرصيد قبل العملية
            $balanceBefore = $wallet->balance;

            // تحديث الرصيد
            $wallet->decrement('balance', $amount);
            $wallet->increment('total_withdrawn', $amount);
            $wallet->update(['last_transaction_at' => now()]);

            // تسجيل المعاملة
            $transaction = $this->transactionService->create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'withdrawal',
                'direction' => 'debit',
                'amount' => $amount,
                'fee' => 0,
                'net_amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
                'metadata' => $metadata,
                'processed_at' => now(),
            ]);

            DB::commit();

            Log::info('Wallet withdrawal successful', [
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ]);

            return $transaction;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Wallet withdrawal failed', [
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * حجز مبلغ في المحفظة (للضمان) ⚠️
     */
    public function reserve(Wallet $wallet, float $amount, array $metadata = []): bool
    {
        DB::beginTransaction();
        
        try {
            if ($amount <= 0) {
                throw new Exception('Invalid reserve amount');
            }

            if (!$this->checkBalance($wallet, $amount)) {
                throw new Exception('Insufficient balance for reservation');
            }

            $balanceBefore = $wallet->balance;

            // نقل المبلغ من الرصيد القابل للسحب إلى المحجوز
            $wallet->decrement('balance', $amount);
            $wallet->increment('reserved_balance', $amount);
            $wallet->update(['last_transaction_at' => now()]);

            // تسجيل المعاملة
            $this->transactionService->create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'escrow_hold',
                'direction' => 'debit',
                'amount' => $amount,
                'fee' => 0,
                'net_amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
                'metadata' => $metadata,
                'processed_at' => now(),
            ]);

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * إطلاق مبلغ محجوز ⚠️
     */
    public function releaseReserved(Wallet $wallet, float $amount, array $metadata = []): bool
    {
        DB::beginTransaction();
        
        try {
            if ($amount <= 0) {
                throw new Exception('Invalid release amount');
            }

            if ($wallet->reserved_balance < $amount) {
                throw new Exception('Insufficient reserved balance');
            }

            $balanceBefore = $wallet->balance;

            // نقل المبلغ من المحجوز إلى القابل للسحب
            $wallet->decrement('reserved_balance', $amount);
            $wallet->increment('balance', $amount);
            $wallet->update(['last_transaction_at' => now()]);

            // تسجيل المعاملة
            $this->transactionService->create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'escrow_release',
                'direction' => 'credit',
                'amount' => $amount,
                'fee' => 0,
                'net_amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
                'metadata' => $metadata,
                'processed_at' => now(),
            ]);

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تجميد المحفظة (للإدارة) ⚠️
     */
    public function freeze(Wallet $wallet, string $reason, ?User $admin): void
    {
        $wallet->update([
            'frozen_at' => now(),
            'freeze_reason' => $reason,
        ]);

        Log::warning('Wallet frozen', [
            'wallet_id' => $wallet->id,
            'reason' => $reason,
            'admin_id' => $admin?->id,
        ]);
    }

    /**
     * إلغاء تجميد المحفظة (للإدارة) ⚠️
     */
    public function unfreeze(Wallet $wallet, ?User $admin): void
    {
        $wallet->update([
            'frozen_at' => null,
            'freeze_reason' => null,
        ]);

        Log::info('Wallet unfrozen', [
            'wallet_id' => $wallet->id,
            'admin_id' => $admin?->id,
        ]);
    }

    /**
     * تحويل داخلي بين محفظتين ⚠️
     */
    public function transfer(Wallet $fromWallet, Wallet $toWallet, float $amount, array $metadata = []): Transaction
    {
        DB::beginTransaction();
        
        try {
            if ($amount <= 0) {
                throw new Exception('Invalid transfer amount');
            }

            if (!$this->isWalletActive($fromWallet) || !$this->isWalletActive($toWallet)) {
                throw new Exception('One or both wallets are not active');
            }

            if (!$this->checkBalance($fromWallet, $amount)) {
                throw new Exception('Insufficient balance');
            }

            // خصم من المحفظة الأولى
            $fromBalanceBefore = $fromWallet->balance;
            $fromWallet->decrement('balance', $amount);
            $fromWallet->update(['last_transaction_at' => now()]);

            // إضافة للمحفظة الثانية
            $toBalanceBefore = $toWallet->balance;
            $toWallet->increment('balance', $amount);
            $toWallet->update(['last_transaction_at' => now()]);

            // تسجيل المعاملة للمحفظة الأولى
            $transaction = $this->transactionService->create([
                'user_id' => $fromWallet->user_id,
                'wallet_id' => $fromWallet->id,
                'type' => 'transfer',
                'direction' => 'debit',
                'amount' => $amount,
                'fee' => 0,
                'net_amount' => $amount,
                'balance_before' => $fromBalanceBefore,
                'balance_after' => $fromWallet->balance,
                'status' => 'completed',
                'metadata' => array_merge($metadata, ['to_wallet_id' => $toWallet->id]),
                'processed_at' => now(),
            ]);

            // تسجيل المعاملة للمحفظة الثانية
            $this->transactionService->create([
                'user_id' => $toWallet->user_id,
                'wallet_id' => $toWallet->id,
                'type' => 'transfer',
                'direction' => 'credit',
                'amount' => $amount,
                'fee' => 0,
                'net_amount' => $amount,
                'balance_before' => $toBalanceBefore,
                'balance_after' => $toWallet->balance,
                'status' => 'completed',
                'metadata' => array_merge($metadata, ['from_wallet_id' => $fromWallet->id]),
                'processed_at' => now(),
            ]);

            DB::commit();

            return $transaction;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على إجمالي الرصيد (قابل للسحب + محجوز)
     */
    public function getTotalBalance(Wallet $wallet): string
    {
        return bcadd($wallet->balance, $wallet->reserved_balance, 2);
    }

    /**
     * التحقق من حدود السحب
     */
    public function checkWithdrawalLimits(Wallet $wallet, float $amount): bool
    {
        if (!$wallet->withdrawal_limit_daily && !$wallet->withdrawal_limit_monthly) {
            return true;
        }

        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $todayWithdrawals = $wallet->user->withdrawalRequests()
            ->where('status', 'completed')
            ->where('created_at', '>=', $today)
            ->sum('amount');

        $monthWithdrawals = $wallet->user->withdrawalRequests()
            ->where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->sum('amount');

        if ($wallet->withdrawal_limit_daily && ($todayWithdrawals + $amount) > $wallet->withdrawal_limit_daily) {
            return false;
        }

        if ($wallet->withdrawal_limit_monthly && ($monthWithdrawals + $amount) > $wallet->withdrawal_limit_monthly) {
            return false;
        }

        return true;
    }
}