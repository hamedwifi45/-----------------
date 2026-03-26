<?php

namespace App\Services\Financial;

use App\Models\Transaction;
use App\Models\User;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TransactionService
{
    /**
     * إنشاء معاملة جديدة ⚠️
     */
    public function create(array $data): Transaction
    {
        DB::beginTransaction();
        
        try {
            // التحقق من البيانات المطلوبة
            $requiredFields = ['user_id', 'wallet_id', 'type', 'direction', 'amount', 'balance_before', 'balance_after'];
            
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new Exception("Missing required field: {$field}");
                }
            }

            // التحقق من صحة المبالغ
            if ($data['amount'] <= 0) {
                throw new Exception('Invalid transaction amount');
            }

            // حساب الصافي تلقائياً إذا لم يتم تحديده
            if (!isset($data['net_amount'])) {
                $data['net_amount'] = $data['amount'] - ($data['fee'] ?? 0);
            }

            // إنشاء المعاملة
            $transaction = Transaction::create([
                'user_id' => $data['user_id'],
                'wallet_id' => $data['wallet_id'],
                'contract_id' => $data['contract_id'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'escrow_id' => $data['escrow_id'] ?? null,
                'withdrawal_request_id' => $data['withdrawal_request_id'] ?? null,
                'type' => $data['type'],
                'direction' => $data['direction'],
                'amount' => $data['amount'],
                'fee' => $data['fee'] ?? 0,
                'net_amount' => $data['net_amount'],
                'balance_before' => $data['balance_before'],
                'balance_after' => $data['balance_after'],
                'reference_id' => $data['reference_id'] ?? $this->generateReferenceId(),
                'external_reference' => $data['external_reference'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'payment_gateway' => $data['payment_gateway'] ?? null,
                'description' => $data['description'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'status' => $data['status'] ?? 'completed',
                'processed_at' => $data['processed_at'] ?? now(),
                'ip_address' => $data['ip_address'] ?? request()->ip(),
                'user_agent' => $data['user_agent'] ?? request()->userAgent(),
                'processed_by' => $data['processed_by'] ?? null,
                'is_locked' => true, // 🔒 قفل المعاملة تلقائياً
            ]);

            DB::commit();

            Log::info('Transaction created', [
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'user_id' => $transaction->user_id,
            ]);

            return $transaction;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Transaction creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * الحصول على معاملة بواسطة ID
     */
    public function getById(int $id): ?Transaction
    {
        return Transaction::findOrFail($id);
    }

    /**
     * الحصول على معاملة بواسطة رقم المرجع
     */
    public function getByReference(string $referenceId): ?Transaction
    {
        return Transaction::where('reference_id', $referenceId)->first();
    }

    /**
     * الحصول على معاملات المستخدم
     */
    public function getUserTransactions(User $user, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Transaction::where('user_id', $user->id);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * الحصول على إجمالي المعاملات لنوع محدد
     */
    public function getTotalByType(User $user, string $type, ?string $startDate = null, ?string $endDate = null): float
    {
        $query = Transaction::where('user_id', $user->id)
            ->where('type', $type)
            ->where('status', 'completed');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->sum('amount');
    }

    /**
     * إنشاء معاملة عكسية (Reversal) ⚠️
     */
    public function createReversal(Transaction $originalTransaction, User $admin, string $reason): Transaction
    {
        DB::beginTransaction();
        
        try {
            // التحقق من صلاحية المدير
            if (!$admin->isAdmin() && !$admin->hasPermission('reverse_transactions')) {
                throw new Exception('Unauthorized to reverse transactions');
            }

            // التحقق من أن المعاملة الأصلية مقفلة
            if (!$originalTransaction->is_locked) {
                throw new Exception('Cannot reverse unlocked transaction');
            }

            // عكس الاتجاه
            $reversedDirection = $originalTransaction->direction === 'credit' ? 'debit' : 'credit';

            // إنشاء المعاملة العكسية
            $reversal = $this->create([
                'user_id' => $originalTransaction->user_id,
                'wallet_id' => $originalTransaction->wallet_id,
                'type' => 'reversal',
                'direction' => $reversedDirection,
                'amount' => $originalTransaction->amount,
                'fee' => 0,
                'net_amount' => $originalTransaction->net_amount,
                'balance_before' => $originalTransaction->balance_after,
                'balance_after' => $originalTransaction->balance_before,
                'reference_id' => $this->generateReferenceId(),
                'description' => "Reversal of transaction #{$originalTransaction->id}: {$reason}",
                'metadata' => [
                    'original_transaction_id' => $originalTransaction->id,
                    'reversal_reason' => $reason,
                    'admin_id' => $admin->id,
                ],
                'processed_by' => $admin->id,
                'processed_at' => now(),
            ]);

            // تحديث المعاملة الأصلية
            $originalTransaction->update([
                'status' => 'reversed',
            ]);

            DB::commit();

            Log::warning('Transaction reversed', [
                'original_transaction_id' => $originalTransaction->id,
                'reversal_transaction_id' => $reversal->id,
                'admin_id' => $admin->id,
                'reason' => $reason,
            ]);

            return $reversal;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تصدير معاملات المستخدم
     */
    public function exportUserTransactions(User $user, array $filters = []): array
    {
        $query = Transaction::where('user_id', $user->id);

        if (isset($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date']);
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * توليد رقم مرجع فريد
     */
    protected function generateReferenceId(): string
    {
        return 'TXN-' . strtoupper( Str::random(12));
    }

    /**
     * التحقق من توازن المحفظة مع المعاملات ⚠️
     */
    public function verifyWalletBalance(int $walletId): bool
    {
        $transactions = Transaction::where('wallet_id', $walletId)
            ->where('status', 'completed')
            ->orderBy('created_at')
            ->get();

        $calculatedBalance = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->direction === 'credit') {
                $calculatedBalance += $transaction->net_amount;
            } else {
                $calculatedBalance -= $transaction->amount;
            }
        }

        $wallet = \App\Models\Wallet::find($walletId);
        
        return bccomp($calculatedBalance, $wallet->balance, 2) === 0;
    }

    /**
     * الحصول على آخر رصيد للمحفظة
     */
    public function getLastBalance(int $walletId): float
    {
        $lastTransaction = Transaction::where('wallet_id', $walletId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastTransaction ? $lastTransaction->balance_after : 0;
    }
}