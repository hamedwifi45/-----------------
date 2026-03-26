<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if(!$user){
            return false;
        }
        if ($user->isAdmin()){
            return true;
        }
        if($user->hasPermission('view_Transactions')){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        
        if(!$user){
            return false;
        }
        if ($user->isAdmin()){
            return true;
        }
        if($user->hasPermission('view_Transactions')){
            return true;
        }
        if($user->id === $transaction->user_id ){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return false;
    }
    // ==========================================
    // صلاحيات مالية حرجة ⚠️ (للقراءة فقط)
    // ==========================================

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض نوع المعاملة
     */
    public function viewType(User $user, Transaction $transaction): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض المبلغ
     */
    public function viewAmount(User $user, Transaction $transaction): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض الرصيد قبل وبعد
     */
    public function viewBalanceHistory(User $user, Transaction $transaction): bool
    {
        // صاحب المعاملة أو الإدارة المالية
        if ($user->id === $transaction->user_id) {
            return true;
        }

        if ($user->hasPermission('view_financials')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض البيانات الوصفية
     */
    public function viewMetadata(User $user, Transaction $transaction): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض المرجع الخارجي
     */
    public function viewExternalReference(User $user, Transaction $transaction): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض معلومات بوابة الدفع
     */
    public function viewGatewayInfo(User $user, Transaction $transaction): bool
    {
        // الإدارة المالية فقط
        if ($user->hasPermission('view_financials')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض عنوان IP
     */
    public function viewIpAddress(User $user, Transaction $transaction): bool
    {
        // الإدارة فقط (أمني)
        if ($user->isAdmin() || $user->hasPermission('view_security_logs')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض User Agent
     */
    public function viewUserAgent(User $user, Transaction $transaction): bool
    {
        // نفس شروط عنوان IP
        return $this->viewIpAddress($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض من قام بالمعالجة
     */
    public function viewProcessor(User $user, Transaction $transaction): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير المعاملات
     */
    public function export(User $user, Transaction $transaction): bool
    {
        // صاحب المعاملة أو الإدارة المالية
        if ($user->id === $transaction->user_id) {
            return true;
        }

        if ($user->hasPermission('export_financial_data')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم طباعة المعاملة
     */
    public function print(User $user, Transaction $transaction): bool
    {
        // نفس شروط التصدير
        return $this->export($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض المعاملات المرتبطة بعقد
     */
    public function viewByContract(User $user, Transaction $transaction): bool
    {
        // إذا كان لديه صلاحية رؤية العقد
        if ($transaction->contract) {
            if ($user->id === $transaction->contract->employer_id || 
                $user->id === $transaction->contract->freelancer_id) {
                return true;
            }
        }

        return $this->view($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض المعاملات المرتبطة بضمان
     */
    public function viewByEscrow(User $user, Transaction $transaction): bool
    {
        // إذا كان لديه صلاحية رؤية الضمان
        if ($transaction->escrow) {
            if ($user->id === $transaction->escrow->employer_id || 
                $user->id === $transaction->escrow->freelancer_id) {
                return true;
            }
        }

        return $this->view($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض المعاملات المرتبطة بسحب
     */
    public function viewByWithdrawal(User $user, Transaction $transaction): bool
    {
        // إذا كان لديه صلاحية رؤية طلب السحب
        if ($transaction->withdrawalRequest) {
            if ($user->id === $transaction->withdrawalRequest->user_id) {
                return true;
            }
        }

        return $this->view($user, $transaction);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الإبلاغ عن معاملة مشبوهة
     */
    public function reportSuspicious(User $user, Transaction $transaction): bool
    {
        // أي مستخدم نشط
        return $user && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم (مدير) وضع علامة مشبوهة
     */
    public function markAsSuspicious(User $user, Transaction $transaction): bool
    {
        // فقط الإدارة المالية العليا
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('flag_suspicious_transactions')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عكس المعاملة
     */
    public function reverse(User $user, Transaction $transaction): bool
    {
        // ⚠️ لا يمكن عكس المعاملات مباشرة
        // يجب إنشاء معاملة جديدة من نوع 'reversal'
        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إنشاء معاملة عكس
     */
    public function createReversal(User $user, Transaction $transaction): bool
    {
        // فقط الإدارة المالية
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('reverse_transactions')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم قفل المعاملة
     */
    public function lock(User $user, Transaction $transaction): bool
    {
        // فقط الإدارة المالية
        return $user->hasPermission('manage_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم فتح المعاملة المقفلة
     */
    public function unlock(User $user, Transaction $transaction): bool
    {
        // فقط المدير العام
        return $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض حالة القفل
     */
    public function viewLockStatus(User $user, Transaction $transaction): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $transaction);
    }
}
