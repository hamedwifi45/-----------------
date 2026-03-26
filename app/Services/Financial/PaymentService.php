<?php

namespace App\Services\Financial;

use App\Models\PaymentLog;
use App\Models\Wallet;
use App\Models\User;
use App\Services\Financial\WalletService;
use App\Services\Financial\TransactionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentService
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
     * معالجة إيداع من بوابة دفع ⚠️
     */
    public function processDeposit(
        User $user, 
        float $amount, 
        string $gateway, 
        string $gatewayTransactionId, 
        string $paymentMethod,
        array $rawResponse
    ): PaymentLog {
        DB::beginTransaction();
        
        try {
            $wallet = $this->walletService->getOrCreateWallet($user);

            // إنشاء سجل الدفع
            $paymentLog = PaymentLog::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'gateway' => $gateway,
                'gateway_transaction_id' => $gatewayTransactionId,
                'payment_method' => $paymentMethod,
                'currency' => 'SAR',
                'amount' => $amount,
                'gateway_fee' => 0,
                'status' => 'success',
                'raw_response' => $rawResponse,
                'raw_request' => [],
                'paid_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // إيداع الأموال في المحفظة
            $transaction = $this->walletService->deposit($wallet, $amount, [
                'payment_log_id' => $paymentLog->id,
                'gateway' => $gateway,
                'gateway_transaction_id' => $gatewayTransactionId,
            ]);

            // ربط المعاملة بسجل الدفع
            $paymentLog->update(['transaction_id' => $transaction->id]);

            DB::commit();

            Log::info('Payment deposit processed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'gateway' => $gateway,
                'gateway_transaction_id' => $gatewayTransactionId,
            ]);

            return $paymentLog;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Payment deposit failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * معالجة فشل الدفع
     */
    public function processFailedPayment(
        User $user, 
        float $amount, 
        string $gateway, 
        string $gatewayTransactionId,
        string $failureMessage
    ): PaymentLog {
        $wallet = $this->walletService->getOrCreateWallet($user);

        return PaymentLog::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'gateway' => $gateway,
            'gateway_transaction_id' => $gatewayTransactionId,
            'payment_method' => 'unknown',
            'currency' => 'SAR',
            'amount' => $amount,
            'gateway_fee' => 0,
            'status' => 'failed',
            'failure_message' => $failureMessage,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * التحقق من معاملة بوابة دفع
     */
    public function verifyGatewayTransaction(string $gateway, string $gatewayTransactionId): bool
    {
        return PaymentLog::where('gateway', $gateway)
            ->where('gateway_transaction_id', $gatewayTransactionId)
            ->where('status', 'success')
            ->exists();
    }

    /**
     * استرداد مدفوعات
     */
    public function refundPayment(PaymentLog $paymentLog, User $admin, string $reason): PaymentLog
    {
        DB::beginTransaction();
        
        try {
            if (!$admin->isAdmin() && !$admin->hasPermission('process_refunds')) {
                throw new Exception('Unauthorized to process refunds');
            }

            if ($paymentLog->status !== 'success') {
                throw new Exception('Can only refund successful payments');
            }

            if ($paymentLog->refunded_at) {
                throw new Exception('Payment already refunded');
            }

            // خصم المبلغ من المحفظة
            $this->walletService->withdraw($paymentLog->wallet, $paymentLog->amount, [
                'refund_payment_log_id' => $paymentLog->id,
                'reason' => $reason,
            ]);

            // تحديث سجل الدفع
            $paymentLog->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            DB::commit();

            Log::info('Payment refunded', [
                'payment_log_id' => $paymentLog->id,
                'amount' => $paymentLog->amount,
                'admin_id' => $admin->id,
            ]);

            return $paymentLog;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}