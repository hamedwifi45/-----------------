<?php

namespace App\Policies;

use App\Models\Escrow;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EscrowPolicy
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
    public function view(User $user, Escrow $escrow): bool
    {
         if (!$user) {
            return false;
        }

        // المدير العام يستطيع رؤية كل الضمانات
        if ($user->isAdmin()) {
            return true;
        }

        // المدير المالي يستطيع رؤية الضمانات
        if ($user->hasPermission('view_financials')) {
            return true;
        }

        // صاحب العمل (صاحب الضمان)
        if ($user->id === $escrow->employer_id) {
            return true;
        }

        // المستقل (المستفيد من الضمان)
        if ($user->id === $escrow->freelancer_id) {
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
    public function update(User $user, Escrow $escrow): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Escrow $escrow): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Escrow $escrow): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Escrow $escrow): bool
    {
        return false;
    }

    // ==========================================
    // صلاحيات مالية حرجة ⚠️
    // ==========================================

    /**
     * تحديد ما إذا كان يمكن للمستخدم تمويل الضمان
     * (صاحب العمل يودع المال)
     */
    public function fund(User $user, Escrow $escrow): bool
    {
        // يجب أن يكون الضمان في حالة انتظار
        if ($escrow->status !== 'pending') {
            return false;
        }

        // فقط صاحب العمل يمكنه التمويل
        if ($user->id !== $escrow->employer_id) {
            return false;
        }

        // يجب أن يكون المستخدم نشطاً وغير محظور
        if (!$user->is_active || $user->is_banned) {
            return false;
        }

        // يجب أن يكون لديه رصيد كافٍ
        if (!$user->wallet || $user->wallet->balance < $escrow->amount) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إطلاق الضمان
     * (صاحب العمل يوافق على العمل ويطلق المال)
     */
    public function release(User $user, Escrow $escrow): bool
    {
        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // المدير المالي
        if ($user->hasPermission('release_escrow')) {
            return true;
        }

        // يجب أن يكون الضمان محجوزاً
        if ($escrow->status !== 'held') {
            return false;
        }

        // فقط صاحب العمل يمكنه الإطلاق (أو المدير)
        if ($user->id !== $escrow->employer_id) {
            return false;
        }

        // لا يمكن الإطلاق إذا كان هناك نزاع
        if ($escrow->isDisputed()) {
            return false;
        }

        // يجب أن يكون هناك تسليم معتمد
        if (!$escrow->contract || !$escrow->contract->deliverables()->where('status', 'approved')->exists()) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم استرداد الضمان
     * (إعادة المال لصاحب العمل)
     */
    public function refund(User $user, Escrow $escrow): bool
    {
        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // المدير المالي
        if ($user->hasPermission('refund_escrow')) {
            return true;
        }

        // يجب أن يكون الضمان محجوزاً
        if ($escrow->status !== 'held') {
            return false;
        }

        // لا يمكن الاسترداد إذا كان هناك نزاع مفتوح
        if ($escrow->isDisputed()) {
            return false;
        }

        // صاحب العمل يمكنه طلب الاسترداد (بشروط)
        if ($user->id === $escrow->employer_id) {
            // يجب أن يوافق المستقل أو يكون هناك سبب مقبول
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم فتح نزاع على الضمان
     */
    public function dispute(User $user, Escrow $escrow): bool
    {
        // يجب أن يكون الضمان محجوزاً
        if ($escrow->status !== 'held') {
            return false;
        }

        // أي من طرفي العقد يمكنه فتح نزاع
        if ($user->id === $escrow->employer_id || $user->id === $escrow->freelancer_id) {
            return $user->is_active && !$user->is_banned;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حل النزاع على الضمان
     * (فقط الإدارة)
     */
    public function resolveDispute(User $user, Escrow $escrow): bool
    {
        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // المدير المالي أو مراقب النزاعات
        if ($user->hasPermission('resolve_disputes')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تنفيذ قرار النزاع
     * (توزيع الأموال بعد النزاع)
     */
    public function executeDisputeResolution(User $user, Escrow $escrow): bool
    {
        // فقط الإدارة المالية
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('execute_financial_resolution')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض سجل معاملات الضمان
     */
    public function viewTransactions(User $user, Escrow $escrow): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $escrow);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تمديد الضمان
     */
    public function extend(User $user, Escrow $escrow): bool
    {
        // المدير العام أو صاحب العمل (قبل الإطلاق)
        if ($user->isAdmin()) {
            return true;
        }

        if ($escrow->status !== 'held') {
            return false;
        }

        return $user->id === $escrow->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إلغاء الضمان
     */
    public function cancel(User $user, Escrow $escrow): bool
    {
        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // فقط قبل التمويل
        if ($escrow->status !== 'pending') {
            return false;
        }

        return $user->id === $escrow->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض ملاحظات الإدارة
     */
    public function viewAdminNotes(User $user, Escrow $escrow): bool
    {
        // فقط الإدارة
        return $user->isAdmin() || $user->hasPermission('view_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير بيانات الضمان
     */
    public function export(User $user, Escrow $escrow): bool
    {
        // فقط الإدارة المالية
        return $user->hasPermission('export_financial_data');
    }
}