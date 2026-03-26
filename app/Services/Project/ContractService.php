<?php

namespace App\Services\Project;

use App\Models\Contract;
use App\Models\Bid;
use App\Models\User;
use App\Services\Financial\EscrowService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ContractService
{
    protected EscrowService $escrowService;

    public function __construct(EscrowService $escrowService)
    {
        $this->escrowService = $escrowService;
    }

    /**
     * إنشاء عقد من عرض مقبول ⚠️
     */
    public function createFromBid(Bid $bid, User $employer): Contract
    {
        DB::beginTransaction();
        
        try {
            // التحقق من الصلاحية
            if ($employer->id !== $bid->project->employer_id) {
                throw new Exception('Unauthorized');
            }

            // التحقق من حالة العرض
            if ($bid->status !== 'pending') {
                throw new Exception('Bid is not available');
            }

            // التحقق من عدم وجود عقد بالفعل
            if ($bid->project->contract) {
                throw new Exception('Project already has a contract');
            }

            // إنشاء العقد
            $contract = Contract::create([
                'project_id' => $bid->project->id,
                'bid_id' => $bid->id,
                'employer_id' => $employer->id,
                'freelancer_id' => $bid->freelancer_id,
                'amount' => $bid->amount,
                'commission' => $bid->amount * config('platform.commission_rate', 0.10),
                'net_amount' => $bid->amount * (1 - config('platform.commission_rate', 0.10)),
                'duration_days' => $bid->duration_days,
                'start_date' => null,
                'due_date' => now()->addDays($bid->duration_days),
                'status' => 'pending',
                'revisions_allowed' => 2,
                'revisions_used' => 0,
            ]);

            // تحديث حالة العرض
            $bid->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            // إغلاق باقي العروض
            $bid->project->bids()
                ->where('id', '!=', $bid->id)
                ->update(['status' => 'rejected']);

            // إنشاء الضمان
            $escrow = $this->escrowService->createEscrow($contract);

            DB::commit();

            Log::info('Contract created', [
                'contract_id' => $contract->id,
                'bid_id' => $bid->id,
                'escrow_id' => $escrow->id,
            ]);

            return $contract;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * إكمال العقد
     */
    public function completeContract(Contract $contract, User $user): Contract
    {
        DB::beginTransaction();
        
        try {
            if ($user->id !== $contract->employer_id && !$user->isAdmin()) {
                throw new Exception('Unauthorized');
            }

            if ($contract->status !== 'active') {
                throw new Exception('Contract is not active');
            }

            $contract->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            Log::info('Contract completed', [
                'contract_id' => $contract->id,
                'completed_at' => $contract->completed_at,
            ]);

            return $contract;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * إلغاء العقد
     */
    public function cancelContract(Contract $contract, User $user, string $reason): Contract
    {
        DB::beginTransaction();
        
        try {
            // التحقق من الصلاحية
            if (!in_array($user->id, [$contract->employer_id, $contract->freelancer_id]) && !$user->isAdmin()) {
                throw new Exception('Unauthorized');
            }

            if (in_array($contract->status, ['completed', 'cancelled'])) {
                throw new Exception('Contract is already closed');
            }

            $contract->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $user->id,
                'cancellation_reason' => $reason,
            ]);

            DB::commit();

            Log::info('Contract cancelled', [
                'contract_id' => $contract->id,
                'user_id' => $user->id,
                'reason' => $reason,
            ]);

            return $contract;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على عقد بواسطة ID
     */
    public function getContractById(int $id): ?Contract
    {
        return Contract::with(['project', 'employer', 'freelancer', 'escrow', 'deliverables'])->find($id);
    }
}