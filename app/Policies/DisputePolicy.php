<?php

namespace App\Policies;

use App\Models\Dispute;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DisputePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if(!$user){
            return false;
        }
        if($user->is_admin){
            return true;
        }
        if($user->hasPermission('view_disputes')){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dispute $dispute): bool
    {
        if(!$user){
            return false;
        }
        if($user->is_admin){
            return true;
        }
        if($user->hasPermission('view_disputes')){
            return true;
        }
        if($user->id === $dispute->employer_id || $user->id === $dispute->freelancer_id){
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

        return true;    
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dispute $dispute): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dispute $dispute): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Dispute $dispute): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Dispute $dispute): bool
    {
        return false;
    }
     public function openOnContract(User $user, Dispute $dispute): bool
    {
        // يجب أن يكون العقد نشطاً أو معلقاً
        if (!in_array($dispute->contract->status, ['active', 'paused'])) {
            return false;
        }

        // فقط أطراف العقد يمكنهم فتح نزاع
        if ($user->id !== $dispute->employer_id && $user->id !== $dispute->freelancer_id) {
            return false;
        }

        // يجب أن يكون المستخدم نشطاً
        if (!$user->is_active || $user->is_banned) {
            return false;
        }

        // لا يمكن فتح أكثر من نزاع لنفس العقد
        if ($dispute->contract->disputes()->where('status', 'open')->exists()) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إضافة أدلة
     */
    public function addEvidence(User $user, Dispute $dispute): bool
    {
        // يجب أن يكون النزاع مفتوحاً
        if (!$dispute->isOpen()) {
            return false;
        }

        // أطراف النزاع أو الإدارة
        if ($user->id === $dispute->employer_id || $user->id === $dispute->freelancer_id) {
            return true;
        }

        if ($user->isAdmin() || $user->hasPermission('manage_disputes')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض الأدلة
     */
    public function viewEvidence(User $user, Dispute $dispute): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف دليل
     */
    public function deleteEvidence(User $user, Dispute $dispute): bool
    {
        // فقط صاحب الدليل أو الإدارة
        if ($user->isAdmin() || $user->hasPermission('manage_disputes')) {
            return true;
        }

        // لا يمكن حذف الأدلة بعد تقديمها (سجل قانوني)
        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعيين مدير للنزاع
     */
    public function assignAdmin(User $user, Dispute $dispute): bool
    {
        // فقط المدير العام
        return $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم (مدير) إدارة النزاع
     */
    public function manage(User $user, Dispute $dispute): bool
    {
        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // مدير النزاعات المعين
        if ($user->hasPermission('manage_disputes')) {
            return true;
        }

        // المدير المعين لهذا النزاع تحديداً
        if ($user->id === $dispute->assigned_admin) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حل النزاع
     */
    public function resolve(User $user, Dispute $dispute): bool
    {
        // يجب أن يكون النزاع مفتوحاً أو تحت المراجعة
        if (!in_array($dispute->status, ['open', 'under_review', 'awaiting_evidence', 'mediation'])) {
            return false;
        }

        // فقط الإدارة
        return $this->manage($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إصدار قرار النزاع
     */
    public function issueDecision(User $user, Dispute $dispute): bool
    {
        // نفس شروط الحل
        return $this->resolve($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تنفيذ قرار النزاع
     * (توزيع الأموال)
     */
    public function executeDecision(User $user, Dispute $dispute): bool
    {
        // يجب أن يكون النزاع محلولاً
        if ($dispute->status !== 'resolved') {
            return false;
        }

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
     * تحديد ما إذا كان يمكن للمستخدم إعادة فتح النزاع
     */
    public function reopen(User $user, Dispute $dispute): bool
    {
        // يجب أن يكون النزاع مغلقاً أو محلولاً
        if (!in_array($dispute->status, ['resolved', 'closed'])) {
            return false;
        }

        // فقط المدير العام
        return $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إغلاق النزاع
     */
    public function close(User $user, Dispute $dispute): bool
    {
        // يجب أن يكون النزاع محلولاً
        if ($dispute->status !== 'resolved') {
            return false;
        }

        // الإدارة
        return $this->manage($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تمديد مهلة النزاع
     */
    public function extendDeadline(User $user, Dispute $dispute): bool
    {
        // يجب أن يكون النزاع مفتوحاً
        if (!$dispute->isOpen()) {
            return false;
        }

        // الإدارة
        return $this->manage($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض ملاحظات الإدارة
     */
    public function viewAdminNotes(User $user, Dispute $dispute): bool
    {
        // فقط الإدارة
        return $user->isAdmin() || $user->hasPermission('manage_disputes');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل ملاحظات الإدارة
     */
    public function updateAdminNotes(User $user, Dispute $dispute): bool
    {
        // فقط الإدارة
        return $this->manage($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض قرار النزاع
     */
    public function viewDecision(User $user, Dispute $dispute): bool
    {
        // أطراف النزاع أو الإدارة
        return $this->view($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الاعتراض على القرار
     */
    public function appeal(User $user, Dispute $dispute): bool
    {
        // يجب أن يكون النزاع محلولاً
        if ($dispute->status !== 'resolved') {
            return false;
        }

        // أطراف النزاع فقط
        if ($user->id === $dispute->employer_id || $user->id === $dispute->freelancer_id) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض سجل المحادثات المرتبطة
     */
    public function viewMessages(User $user, Dispute $dispute): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض التسليمات المرتبطة
     */
    public function viewDeliverables(User $user, Dispute $dispute): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير النزاع
     */
    public function export(User $user, Dispute $dispute): bool
    {
        // فقط الإدارة
        return $user->hasPermission('export_disputes');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم وضع النزاع كأولوية عالية
     */
    public function flagAsUrgent(User $user, Dispute $dispute): bool
    {
        // الإدارة فقط
        return $this->manage($user, $dispute);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديد النزاع كمشتبه به
     */
    public function markAsSuspicious(User $user, Dispute $dispute): bool
    {
        // فقط الإدارة العليا
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('flag_suspicious_activities')) {
            return true;
        }

        return false;
    }

}
