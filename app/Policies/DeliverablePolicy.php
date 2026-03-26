<?php

namespace App\Policies;

use App\Models\Deliverable;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeliverablePolicy
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
    public function view(User $user, Deliverable $deliverable): bool
    {
        if (!$user) {
            return false;
        }

        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // أطراف العقد
        if ($user->id === $deliverable->contract->employer_id || 
            $user->id === $deliverable->contract->freelancer_id) {
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
    public function update(User $user, Deliverable $deliverable): bool
    {
         // لا يمكن تعديل التسليم بعد تقديمه (سجل عمل)
        // إلا إذا كان في حالة مسودة
        if ($deliverable->status !== 'draft') {
            return false;
        }

        // فقط المستقل الذي قدم التسليم
        return $user->id === $deliverable->freelancer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Deliverable $deliverable): bool
    {
         // لا يمكن حذف التسليمات بعد تقديمها
        if ($deliverable->status !== 'draft') {
            return false;
        }

        // فقط المستقل أو الإدارة
        return $user->id === $deliverable->freelancer_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Deliverable $deliverable): bool
    {
        return $user->id === $deliverable->freelancer_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Deliverable $deliverable): bool
    {
        return $user->isAdmin();
    }
    /**
     * تحديد ما إذا كان يمكن للمستخدم تسليم عمل لعقد
     */
    public function submitForContract(User $user, Deliverable $deliverable): bool
    {
        // يجب أن يكون المستخدم هو المستقل في العقد
        if ($user->id !== $deliverable->contract->freelancer_id) {
            return false;
        }

        // يجب أن يكون العقد نشطاً
        if ($deliverable->contract->status !== 'active') {
            return false;
        }

        // لا يمكن التسليم إذا كان هناك نزاع
        if ($deliverable->contract->isDisputed()) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم مراجعة التسليم
     */
    public function review(User $user, Deliverable $deliverable): bool
    {
        // فقط صاحب العمل يمكنه المراجعة
        return $user->id === $deliverable->contract->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الموافقة على التسليم
     */
    public function approve(User $user, Deliverable $deliverable): bool
    {
        // نفس شروط المراجعة
        return $this->review($user, $deliverable);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم رفض التسليم
     */
    public function reject(User $user, Deliverable $deliverable): bool
    {
        // نفس شروط المراجعة
        return $this->review($user, $deliverable);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم طلب تعديل على التسليم
     */
    public function requestRevision(User $user, Deliverable $deliverable): bool
    {
        // نفس شروط المراجعة
        return $this->review($user, $deliverable);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض ملاحظات العميل
     */
    public function viewClientFeedback(User $user, Deliverable $deliverable): bool
    {
        // أطراف العقد أو الإدارة
        return $this->view($user, $deliverable);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض رد المستقل
     */
    public function viewFreelancerResponse(User $user, Deliverable $deliverable): bool
    {
        // أطراف العقد أو الإدارة
        return $this->view($user, $deliverable);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحميل ملفات التسليم
     */
    public function downloadFiles(User $user, Deliverable $deliverable): bool
    {
        // أطراف العقد أو الإدارة
        return $this->view($user, $deliverable);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم رفع ملفات إضافية
     */
    public function uploadFiles(User $user, Deliverable $deliverable): bool
    {
        // فقط المستقل (قبل الموافقة النهائية)
        if ($user->id === $deliverable->freelancer_id) {
            if (in_array($deliverable->status, ['submitted', 'revision_requested'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديد التسليم كنهائي
     */
    public function markAsFinal(User $user, Deliverable $deliverable): bool
    {
        // فقط المستقل
        return $user->id === $deliverable->freelancer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تمديد مهلة التسليم
     */
    public function extendDeadline(User $user, Deliverable $deliverable): bool
    {
        // الإدارة أو أطراف العقد (بموافقة الطرف الآخر)
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $deliverable->contract->employer_id || 
            $user->id === $deliverable->contract->freelancer_id) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض سجل إصدارات التسليم
     */
    public function viewVersionHistory(User $user, Deliverable $deliverable): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $deliverable);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم استعادة إصدار سابق
     */
    public function revertToVersion(User $user, Deliverable $deliverable): bool
    {
        // فقط المستقل أو الإدارة
        return $user->id === $deliverable->freelancer_id || $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم استخدام التسليم كأدلة في نزاع
     */
    public function useAsEvidence(User $user, Deliverable $deliverable): bool
    {
        // أطراف العقد
        return $this->view($user, $deliverable);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الإبلاغ عن تسليم مخالف
     */
    public function report(User $user, Deliverable $deliverable): bool
    {
        // أي مستخدم نشط
        return $user && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم (مدير) التعامل مع بلاغ
     */
    public function handleReport(User $user, Deliverable $deliverable): bool
    {
        // فقط الإدارة
        return $user->hasPermission('manage_reports');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير التسليم
     */
    public function export(User $user, Deliverable $deliverable): bool
    {
        // أطراف العقد أو الإدارة
        return $this->view($user, $deliverable);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم طباعة التسليم
     */
    public function print(User $user, Deliverable $deliverable): bool
    {
        // نفس شروط التصدير
        return $this->export($user, $deliverable);
    }
}
