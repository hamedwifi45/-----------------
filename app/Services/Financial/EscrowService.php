<?php

namespace App\Services\Financial;

use App\Models\Escrow;
use App\Models\Contract;
use App\Models\User;
use App\Services\Financial\WalletService;
use App\Services\Financial\TransactionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class EscrowService
{
    protected WalletService $walletService;
    protected TransactionService $transactionService;

    public function __construct(
        WalletService $walletService,
        TransactionService $transactionService
    ) {
        $this->walletService = $walletService;
        $this->transactionService = $transactionService;
    }

    /**
     * إنشاء ضمان جديد للعقد ⚠️
     */
    public function createEscrow(Contract $contract): Escrow
    {
        $commissionRate = config('platform.commission_rate', 0.10); // 10% افتراضي
        $commission = $contract->amount * $commissionRate;
        $netAmount = $contract->amount - $commission;

        return Escrow::create([
            'contract_id' => $contract->id,
            'employer_id' => $contract->employer_id,
            'freelancer_id' => $contract->freelancer_id,
            'wallet_id' => $contract->employer->wallet->id,
            'amount' => $contract->amount,
            'commission' => $commission,
            'net_amount' => $netAmount,
            'refunded_amount' => 0,
            'status' => 'pending',
            'reference_id' => 'ESC-' . strtoupper(Str::random(12)),
        ]);
    }

    /**
     * تمويل الضمان (إيداع المال من صاحب العمل) ⚠️
     */
    public function fundEscrow(Escrow $escrow, User $employer): Escrow
    {
        DB::beginTransaction();
        
        try {
            // التحقق من أن المستخدم هو صاحب العمل
            if ($employer->id !== $escrow->employer_id) {
                throw new Exception('Unauthorized user');
            }

            // التحقق من حالة الضمان
            if ($escrow->status !== 'pending') {
                throw new Exception('Escrow is not in pending status');
            }

            // التحقق من المحفظة
            $wallet = $employer->wallet;
            if (!$wallet || !$this->walletService->isWalletActive($wallet)) {
                throw new Exception('Invalid wallet');
            }

            // التحقق من الرصيد الكافي
            if (!$this->walletService->checkBalance($wallet, $escrow->amount)) {
                throw new Exception('Insufficient balance');
            }

            // حجز المبلغ في محفظة صاحب العمل
            $this->walletService->reserve($wallet, $escrow->amount, [
                'escrow_id' => $escrow->id,
                'contract_id' => $escrow->contract_id,
            ]);

            // تحديث حالة الضمان
            $escrow->update([
                'status' => 'held',
                'funded_at' => now(),
            ]);

            // تحديث حالة العقد
            $escrow->contract->update([
                'status' => 'active',
                'start_date' => now(),
            ]);

            DB::commit();

            Log::info('Escrow funded successfully', [
                'escrow_id' => $escrow->id,
                'amount' => $escrow->amount,
                'employer_id' => $employer->id,
            ]);

            return $escrow;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Escrow funding failed', [
                'escrow_id' => $escrow->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * إطلاق الضمان للمستقل ⚠️
     */
    public function releaseEscrow(Escrow $escrow, User $releaser, ?string $note = null): Escrow
    {
        DB::beginTransaction();
        
        try {
            // التحقق من حالة الضمان
            if ($escrow->status !== 'held') {
                throw new Exception('Escrow is not in held status');
            }

            // التحقق من وجود نزاع
            if ($escrow->isDisputed()) {
                throw new Exception('Cannot release escrow with active dispute');
            }

            // التحقق من الصلاحية (صاحب العمل أو مدير)
            if ($releaser->id !== $escrow->employer_id && !$releaser->isAdmin()) {
                throw new Exception('Unauthorized user');
            }

            // تحويل المبلغ المحجوز إلى صاحب العمل (إلغاء الحجز)
            $employerWallet = $escrow->employer->wallet;
            $this->walletService->releaseReserved($employerWallet, $escrow->amount, [
                'escrow_id' => $escrow->id,
                'action' => 'release_to_freelancer',
            ]);

            // خصم العمولة وتحويل الصافي للمستقل
            $freelancerWallet = $escrow->freelancer->wallet;
            
            // خصم المبلغ من صاحب العمل (العمولة + الصافي)
            $this->walletService->withdraw($employerWallet, $escrow->amount, [
                'escrow_id' => $escrow->id,
                'type' => 'escrow_payment',
            ]);

            // إضافة الصافي للمستقل
            $this->walletService->deposit($freelancerWallet, $escrow->net_amount, [
                'escrow_id' => $escrow->id,
                'contract_id' => $escrow->contract_id,
            ]);

            // تسجيل عمولة المنصة (تذهب لمحفظة المنصة)
            $platformWallet = $this->getPlatformWallet();
            if ($platformWallet && $escrow->commission > 0) {
                $this->walletService->deposit($platformWallet, $escrow->commission, [
                    'escrow_id' => $escrow->id,
                    'type' => 'platform_commission',
                ]);
            }

            // تحديث حالة الضمان
            $escrow->update([
                'status' => 'released',
                'released_at' => now(),
                'released_by' => $releaser->id,
                'release_note' => $note,
            ]);

            // تحديث حالة العقد
            $escrow->contract->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            Log::info('Escrow released successfully', [
                'escrow_id' => $escrow->id,
                'amount' => $escrow->amount,
                'freelancer_id' => $escrow->freelancer_id,
                'releaser_id' => $releaser->id,
            ]);

            return $escrow;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Escrow release failed', [
                'escrow_id' => $escrow->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * استرداد الضمان لصاحب العمل ⚠️
     */
    public function refundEscrow(Escrow $escrow, User $refunder, ?string $note = null): Escrow
    {
        DB::beginTransaction();
        
        try {
            // التحقق من حالة الضمان
            if ($escrow->status !== 'held') {
                throw new Exception('Escrow is not in held status');
            }

            // التحقق من الصلاحية
            if ($refunder->id !== $escrow->employer_id && !$refunder->isAdmin()) {
                throw new Exception('Unauthorized user');
            }

            // إطلاق المبلغ المحجوز لصاحب العمل
            $employerWallet = $escrow->employer->wallet;
            $this->walletService->releaseReserved($employerWallet, $escrow->amount, [
                'escrow_id' => $escrow->id,
                'action' => 'refund_to_employer',
            ]);

            // تحديث حالة الضمان
            $escrow->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'refunded_by' => $refunder->id,
                'refunded_amount' => $escrow->amount,
                'refund_note' => $note,
            ]);

            // تحديث حالة العقد
            $escrow->contract->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $refunder->id,
            ]);

            DB::commit();

            Log::info('Escrow refunded successfully', [
                'escrow_id' => $escrow->id,
                'amount' => $escrow->amount,
                'employer_id' => $escrow->employer_id,
                'refunder_id' => $refunder->id,
            ]);

            return $escrow;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Escrow refund failed', [
                'escrow_id' => $escrow->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * فتح نزاع على الضمان ⚠️
     */
    public function disputeEscrow(Escrow $escrow, User $disputer, string $reason): Escrow
    {
        // التحقق من الصلاحية
        if ($disputer->id !== $escrow->employer_id && $disputer->id !== $escrow->freelancer_id) {
            throw new Exception('Unauthorized user');
        }

        // التحقق من حالة الضمان
        if ($escrow->status !== 'held') {
            throw new Exception('Can only dispute held escrows');
        }

        $escrow->update([
            'status' => 'disputed',
            'disputed_by' => $disputer->id,
        ]);

        Log::warning('Escrow disputed', [
            'escrow_id' => $escrow->id,
            'disputer_id' => $disputer->id,
            'reason' => $reason,
        ]);

        return $escrow;
    }

    /**
     * حل نزاع الضمان (للإدارة) ⚠️
     */
    public function resolveDispute(
        Escrow $escrow, 
        User $admin, 
        string $resolution, 
        ?float $employerAmount = null, 
        ?float $freelancerAmount = null
    ): Escrow {
        DB::beginTransaction();
        
        try {
            // التحقق من صلاحية المدير
            if (!$admin->isAdmin() && !$admin->hasPermission('resolve_disputes')) {
                throw new Exception('Unauthorized admin');
            }

            // التحقق من حالة النزاع
            if ($escrow->status !== 'disputed') {
                throw new Exception('Escrow is not in dispute');
            }

            // توزيع الأموال حسب القرار
            if ($employerAmount !== null && $employerAmount > 0) {
                $employerWallet = $escrow->employer->wallet;
                $this->walletService->releaseReserved($employerWallet, $employerAmount, [
                    'escrow_id' => $escrow->id,
                    'dispute_resolution' => true,
                ]);
            }

            if ($freelancerAmount !== null && $freelancerAmount > 0) {
                $freelancerWallet = $escrow->freelancer->wallet;
                $this->walletService->deposit($freelancerWallet, $freelancerAmount, [
                    'escrow_id' => $escrow->id,
                    'dispute_resolution' => true,
                ]);
            }

            // تحديث حالة الضمان
            $escrow->update([
                'status' => 'released',
                'released_at' => now(),
                'released_by' => $admin->id,
                'release_note' => "Dispute resolved by admin: {$resolution}",
            ]);

            DB::commit();

            Log::info('Escrow dispute resolved', [
                'escrow_id' => $escrow->id,
                'admin_id' => $admin->id,
                'resolution' => $resolution,
                'employer_amount' => $employerAmount,
                'freelancer_amount' => $freelancerAmount,
            ]);

            return $escrow;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على محفظة المنصة
     */
    protected function getPlatformWallet()
    {
        $platformUser = User::where('email', config('platform.admin_email'))->first();
        return $platformUser?->wallet;
    }

    /**
     * الحصول على ضمان بواسطة رقم المرجع
     */
    public function getByReference(string $referenceId): ?Escrow
    {
        return Escrow::where('reference_id', $referenceId)->first();
    }

    /**
     * التحقق من أن الضمان ينتمي للمستخدم
     */
    public function belongsToUser(Escrow $escrow, User $user): bool
    {
        return $user->id === $escrow->employer_id || 
               $user->id === $escrow->freelancer_id || 
               $user->isAdmin();
    }
}