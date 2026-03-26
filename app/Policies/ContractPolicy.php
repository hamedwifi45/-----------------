<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContractPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contract $contract): bool
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

        // أطراف العقد
        if ($user->id === $contract->employer_id || $user->id === $contract->freelancer_id) {
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
    public function update(User $user, Contract $contract): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contract $contract): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contract $contract): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contract $contract): bool
    {
        return false;
    }
// ==========================================
    // صلاحيات تشغيلية حرجة ⚠️
    // ==========================================

    /**
     * تحديد ما إذا كان يمكن للمستخدم بدء العقد
     * (تفعيل بعد إيداع الضمان)
     */
    public function activate(User $user, Contract $contract): bool
    {
        // يجب أن يكون العقد في حالة انتظار
        if ($contract->status !== 'pending') {
            return false;
        }

        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // صاحب العمل (بعد إيداع الضمان)
        if ($user->id === $contract->employer_id) {
            return $contract->escrow && $contract->escrow->isHeld();
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إكمال العقد
     */
    public function complete(User $user, Contract $contract): bool
    {
        // يجب أن يكون العقد نشطاً
        if ($contract->status !== 'active') {
            return false;
        }

        // لا يمكن الإكمال إذا كان هناك نزاع
        if ($contract->isDisputed()) {
            return false;
        }

        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // صاحب العمل (بعد الموافقة على التسليم النهائي)
        if ($user->id === $contract->employer_id) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إلغاء العقد
     */
    public function cancel(User $user, Contract $contract): bool
    {
        // لا يمكن إلغاء عقد مكتمل
        if (in_array($contract->status, ['completed', 'cancelled'])) {
            return false;
        }

        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // أطراف العقد (بشروط)
        if ($user->id === $contract->employer_id || $user->id === $contract->freelancer_id) {
            // يجب أن يوافق الطرف الآخر أو يكون هناك سبب مقبول
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعليق العقد
     */
    public function pause(User $user, Contract $contract): bool
    {
        // يجب أن يكون العقد نشطاً
        if ($contract->status !== 'active') {
            return false;
        }

        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // أطراف العقد (بموافقة الطرف الآخر)
        if ($user->id === $contract->employer_id || $user->id === $contract->freelancer_id) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم استئناف العقد
     */
    public function resume(User $user, Contract $contract): bool
    {
        // يجب أن يكون العقد معلقاً
        if ($contract->status !== 'paused') {
            return false;
        }

        // نفس شروط التعليق
        return $this->pause($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تمديد العقد
     */
    public function extend(User $user, Contract $contract): bool
    {
        // يجب أن يكون العقد نشطاً
        if ($contract->status !== 'active') {
            return false;
        }

        // المدير العام أو أطراف العقد
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $contract->employer_id || $user->id === $contract->freelancer_id) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل مدة العقد
     */
    public function updateDuration(User $user, Contract $contract): bool
    {
        // نفس شروط التمديد
        return $this->extend($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل مبلغ العقد
     */
    public function updateAmount(User $user, Contract $contract): bool
    {
        // لا يمكن تعديل المبلغ الأساسي
        // يجب إنشاء عقد تعديل منفصل
        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض التسليمات
     */
    public function viewDeliverables(User $user, Contract $contract): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تسليم عمل
     */
    public function submitDeliverable(User $user, Contract $contract): bool
    {
        // يجب أن يكون العقد نشطاً
        if (!in_array($contract->status, ['active'])) {
            return false;
        }

        // لا يمكن التسليم إذا كان هناك نزاع
        if ($contract->isDisputed()) {
            return false;
        }

        // فقط المستقل يمكنه التسليم
        return $user->id === $contract->freelancer_id && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم مراجعة التسليم
     */
    public function reviewDeliverable(User $user, Contract $contract): bool
    {
        // يجب أن يكون العقد نشطاً
        if ($contract->status !== 'active') {
            return false;
        }

        // فقط صاحب العمل يمكنه المراجعة
        return $user->id === $contract->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم قبول التسليم
     */
    public function approveDeliverable(User $user, Contract $contract): bool
    {
        // نفس شروط المراجعة
        return $this->reviewDeliverable($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم رفض التسليم
     */
    public function rejectDeliverable(User $user, Contract $contract): bool
    {
        // نفس شروط المراجعة
        return $this->reviewDeliverable($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم طلب تعديل على التسليم
     */
    public function requestRevision(User $user, Contract $contract): bool
    {
        // نفس شروط المراجعة
        return $this->reviewDeliverable($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم فتح نزاع على العقد
     */
    public function dispute(User $user, Contract $contract): bool
    {
        // يجب أن يكون العقد نشطاً أو معلقاً
        if (!in_array($contract->status, ['active', 'paused'])) {
            return false;
        }

        // أطراف العقد فقط
        if ($user->id === $contract->employer_id || $user->id === $contract->freelancer_id) {
            return $user->is_active && !$user->is_banned;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض الملاحظات الإدارية
     */
    public function viewAdminNotes(User $user, Contract $contract): bool
    {
        // فقط الإدارة
        return $user->isAdmin() || $user->hasPermission('view_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل الملاحظات الإدارية
     */
    public function updateAdminNotes(User $user, Contract $contract): bool
    {
        // فقط الإدارة
        return $user->hasPermission('manage_contracts');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض سجل التعديلات
     */
    public function viewAmendments(User $user, Contract $contract): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إنشاء تعديل على العقد
     */
    public function createAmendment(User $user, Contract $contract): bool
    {
        // المدير العام أو أطراف العقد (بموافقة الطرف الآخر)
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $contract->employer_id || $user->id === $contract->freelancer_id) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض المعاملات المالية المرتبطة
     */
    public function viewTransactions(User $user, Contract $contract): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض الضمان المرتبط
     */
    public function viewEscrow(User $user, Contract $contract): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إصدار فاتورة
     */
    public function invoice(User $user, Contract $contract): bool
    {
        // فقط الإدارة المالية
        if ($user->hasPermission('manage_invoices')) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير العقد
     */
    public function export(User $user, Contract $contract): bool
    {
        // أطراف العقد أو الإدارة
        if ($user->id === $contract->employer_id || $user->id === $contract->freelancer_id) {
            return true;
        }

        if ($user->hasPermission('export_contracts')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم طباعة العقد
     */
    public function print(User $user, Contract $contract): bool
    {
        // نفس شروط التصدير
        return $this->export($user, $contract);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تقييم العقد
     * (بعد الإكمال)
     */
    public function review(User $user, Contract $contract): bool
    {
        // يجب أن يكون العقد مكتملاً
        if ($contract->status !== 'completed') {
            return false;
        }

        // أطراف العقد فقط
        if ($user->id === $contract->employer_id || $user->id === $contract->freelancer_id) {
            return true;
        }

        return false;
    }
}
