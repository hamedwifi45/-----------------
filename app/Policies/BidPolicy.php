<?php

namespace App\Policies;

use App\Models\Bid;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BidPolicy
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
    public function view(User $user, Bid $bid): bool
    {
         // إذا لم يكن المستخدم مسجلاً، لا يمكنه العرض
        if (!$user) {
            return false;
        }

        // المدير العام يستطيع رؤية كل العروض
        if ($user->isAdmin()) {
            return true;
        }

        // المستقل الذي قدم العرض يمكنه رؤية عرضه
        if ($user->id === $bid->freelancer_id) {
            return true;
        }

        // صاحب العمل (صاحب المشروع) يمكنه رؤية العروض على مشروعه
        if ($user->id === $bid->project->employer_id) {
            return true;
        }

        // إذا كان هناك عقد نشط، أطراف العقد يمكنهم الرؤية
        if ($bid->contract && $bid->contract->isActive()) {
            if ($user->id === $bid->contract->employer_id || 
                $user->id === $bid->contract->freelancer_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
         // يجب أن يكون المستخدم مفعلاً وغير محظور
        if (!$user->is_active || $user->is_banned) {
            return false;
        }

        // يجب أن يكون مستقلاً
        return $user->isFreelancer();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bid $bid): bool
    {
         // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // لا يمكن تعديل العرض بعد قبوله أو رفضه
        if (!in_array($bid->status, ['pending'])) {
            return false;
        }

        // فقط المستقل الذي قدم العرض يمكنه تعديله
        return $user->id === $bid->freelancer_id;   
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bid $bid): bool
    {
         // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // لا يمكن حذف العرض إذا تم قبوله (هناك عقد)
        if ($bid->status === 'accepted') {
            return false;
        }

        // فقط المستقل الذي قدم العرض يمكنه حذفه
        return $user->id === $bid->freelancer_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bid $bid): bool
    {
        return $user->isAdmin() || $user->id === $bid->freelancer_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bid $bid): bool
    {
        return $user->isAdmin();
    }
    // ==========================================
    // صلاحيات مخصصة للمنصة
    // ==========================================

    /**
     * تحديد ما إذا كان يمكن للمستخدم سحب العرض
     */
    public function withdraw(User $user, Bid $bid): bool
    {
        // يجب أن يكون العرض قيد الانتظار
        if ($bid->status !== 'pending') {
            return false;
        }

        // فقط المستقل الذي قدم العرض يمكنه سحبه
        return $user->id === $bid->freelancer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم قبول العرض
     * (صاحب العمل يقبل عرض المستقل)
     */
    public function accept(User $user, Bid $bid): bool
    {
        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // يجب أن يكون العرض قيد الانتظار
        if ($bid->status !== 'pending') {
            return false;
        }

        // فقط صاحب العمل (صاحب المشروع) يمكنه قبول العرض
        return $user->id === $bid->project->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم رفض العرض
     */
    public function reject(User $user, Bid $bid): bool
    {
        // نفس شروط القبول
        return $this->accept($user, $bid);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض العروض على مشروعه
     * (تحقق إضافي من جانب المشروع)
     */
    public function viewProjectBids(User $user, Bid $bid): bool
    {
        // المدير العام أو صاحب العمل
        return $user->isAdmin() || $user->id === $bid->project->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تقديم عرض على مشروع
     * (تحقق من جانب المشروع أيضاً)
     */
    public function placeOnProject(User $user, Bid $bid): bool
    {
        // يجب أن يكون المستخدم هو مقدم العرض
        if ($user->id !== $bid->freelancer_id) {
            return false;
        }

        // التحقق من حالة المشروع
        if (!$bid->project->isOpen()) {
            return false;
        }

        // لا يمكن لصاحب العمل التقديم على مشروعه
        if ($user->id === $bid->project->employer_id) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل سعر العرض
     */
    public function updateAmount(User $user, Bid $bid): bool
    {
        // نفس شروط التعديل العام
        return $this->update($user, $bid);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل مدة التنفيذ
     */
    public function updateDuration(User $user, Bid $bid): bool
    {
        // نفس شروط التعديل العام
        return $this->update($user, $bid);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم رؤية ملاحظات صاحب العمل
     */
    public function viewEmployerFeedback(User $user, Bid $bid): bool
    {
        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // المستقل الذي قدم العرض يمكنه رؤية الملاحظات
        if ($user->id === $bid->freelancer_id) {
            return true;
        }

        // صاحب العمل يمكنه رؤية ملاحظاته
        if ($user->id === $bid->project->employer_id) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم رؤية تقييم العرض
     */
    public function viewRating(User $user, Bid $bid): bool
    {
        // نفس شروط رؤية الملاحظات
        return $this->viewEmployerFeedback($user, $bid);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تمديد صلاحية العرض
     */
    public function extendExpiry(User $user, Bid $bid): bool
    {
        // المدير العام أو المستقل (قبل القبول)
        if ($user->isAdmin()) {
            return true;
        }

        if ($bid->status !== 'pending') {
            return false;
        }

        return $user->id === $bid->freelancer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحويل العرض لعقد
     * (هذا يحدث تلقائياً عند القبول، لكن نضع الصلاحية للتحقق)
     */
    public function convertToContract(User $user, Bid $bid): bool
    {
        // نفس شروط القبول
        return $this->accept($user, $bid);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض تاريخ العرض
     * (متى قدم، متى قبل، إلخ)
     */
    public function viewHistory(User $user, Bid $bid): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $bid);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الإبلاغ عن عرض مخالف
     */
    public function report(User $user, Bid $bid): bool
    {
        // أي مستخدم مسجل يمكنه الإبلاغ
        return $user && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم (مدير) التعامل مع بلاغ
     */
    public function handleReport(User $user, Bid $bid): bool
    {
        // فقط المدير العام
        return $user->isAdmin();
    }
}
