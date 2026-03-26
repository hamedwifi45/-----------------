<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Auth\Access\Response;

class WithdrawalRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
         // فقط الإدارة المالية يمكنها عرض قائمة طلبات السحب
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('view_withdrawal_requests')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WithdrawalRequest $request): bool
    {
        if (!$user) {
            return false;
        }

        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // المدير المالي
        if ($user->hasPermission('view_financials')) {
            return true;
        }

        // صاحب طلب السحب
        if ($user->id === $request->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
         // يجب أن يكون المستخدم نشطاً وغير محظور
        if (!$user->is_active || $user->is_banned) {
            return false;
        }

        // يجب أن يكون مستقلاً (لأنه يسحب أرباح)
        if (!$user->isFreelancer()) {
            return false;
        }

        // يجب أن يكون الملف الشخصي موثقاً
        if (!$user->profile || !$user->profile->is_verified) {
            return false;
        }

        // يجب أن تكون المحفظة نشطة وغير مجمدة
        if (!$user->wallet || !$user->wallet->is_active || $user->wallet->isFrozen()) {
            return false;
        }

        // يجب أن يكون مسموحاً بالسحب
        if (!$user->wallet->can_withdraw) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WithdrawalRequest $request): bool
    {
         // يمكن إلغاء الطلب فقط إذا كان قيد الانتظار
        if ($request->status !== 'pending') {
            return false;
        }

        // صاحب الطلب أو المدير العام
        return $user->id === $request->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return false;
    }
    // ==========================================
    // صلاحيات مالية حرجة ⚠️ (للإدارة فقط)
    // ==========================================

    /**
     * تحديد ما إذا كان يمكن للمستخدم الموافقة على طلب السحب
     */
    public function approve(User $user, WithdrawalRequest $request): bool
    {
        // يجب أن يكون الطلب قيد الانتظار
        if ($request->status !== 'pending') {
            return false;
        }

        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // المدير المالي
        if ($user->hasPermission('approve_withdrawals')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم رفض طلب السحب
     */
    public function reject(User $user, WithdrawalRequest $request): bool
    {
        // نفس شروط الموافقة
        return $this->approve($user, $request);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم معالجة طلب السحب
     * (تنفيذ التحويل البنكي الفعلي)
     */
    public function process(User $user, WithdrawalRequest $request): bool
    {
        // يجب أن يكون الطلب موافقاً عليه
        if ($request->status !== 'approved') {
            return false;
        }

        // المدير المالي فقط
        if ($user->hasPermission('process_withdrawals')) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إكمال طلب السحب
     * (بعد نجاح التحويل)
     */
    public function complete(User $user, WithdrawalRequest $request): bool
    {
        // نفس شروط المعالجة
        return $this->process($user, $request);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إلغاء طلب السحب
     * (من قبل الإدارة)
     */
    public function cancel(User $user, WithdrawalRequest $request): bool
    {
        // المدير العام أو المالي
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('manage_withdrawals')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل مبلغ السحب
     */
    public function updateAmount(User $user, WithdrawalRequest $request): bool
    {
        // لا يمكن تعديل المبلغ أبداً (سجل مالي)
        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل طريقة السحب
     */
    public function updateMethod(User $user, WithdrawalRequest $request): bool
    {
        // لا يمكن تعديل الطريقة بعد الإنشاء
        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض تفاصيل الدفع
     * (بيانات الحساب البنكي الحساسة)
     */
    public function viewPaymentDetails(User $user, WithdrawalRequest $request): bool
    {
        // فقط الإدارة المالية
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('view_financial_details')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض ملاحظات الإدارة
     */
    public function viewAdminNotes(User $user, WithdrawalRequest $request): bool
    {
        // فقط الإدارة
        return $user->isAdmin() || $user->hasPermission('view_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل ملاحظات الإدارة
     */
    public function updateAdminNotes(User $user, WithdrawalRequest $request): bool
    {
        // فقط الإدارة المالية
        return $user->hasPermission('manage_withdrawals');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض سبب الرفض
     */
    public function viewRejectionReason(User $user, WithdrawalRequest $request): bool
    {
        // صاحب الطلب أو الإدارة
        return $user->id === $request->user_id || 
               $user->isAdmin() || 
               $user->hasPermission('view_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض رقم المعاملة الخارجية
     */
    public function viewTransactionReference(User $user, WithdrawalRequest $request): bool
    {
        // صاحب الطلب أو الإدارة
        return $this->view($user, $request);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير طلبات السحب
     */
    public function export(User $user, WithdrawalRequest $request): bool
    {
        // فقط الإدارة المالية
        return $user->hasPermission('export_financial_data');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم وضع الطلب في قائمة المراجعة
     */
    public function flagForReview(User $user, WithdrawalRequest $request): bool
    {
        // أي موظف مالي
        if ($user->hasPermission('view_financials')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديد طلب سحب كمشتبه به
     */
    public function markAsSuspicious(User $user, WithdrawalRequest $request): bool
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
}
