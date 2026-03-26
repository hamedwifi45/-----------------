<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return True;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // إذا كان المشروع مخفي، فقط صاحب العمل أو المدير يمكنه رؤيته
        if ($project->is_hidden) {
            return $user && ($user->id === $project->employer_id || $user->isAdmin());
        }

        // إذا كان المشروع غير منشور، فقط صاحب العمل أو المدير
        if ($project->status === 'draft') {
            return $user && ($user->id === $project->employer_id || $user->isAdmin());
        }

        // المشاريع المنشورة والعامة متاحة للجميع
        return true;
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

        // يجب أن يكون صاحب عمل (وليس مستقلاً فقط)
        return $user->role === 'employer' || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
         // المدير العام يستطيع كل شيء
        if ($user->isAdmin()) {
            return true;
        }

        // لا يمكن تعديل المشروع إذا كان هناك عقد نشط
        if ($project->contract && $project->contract->isActive()) {
            return false;
        }

        // فقط صاحب العمل يمكنه تعديل مشروعه
        return $user->id === $project->employer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
         // المدير العام يستطيع كل شيء
        if ($user->isAdmin()) {
            return true;
        }

        // لا يمكن حذف المشروع إذا كان هناك عروض مقدمة
        if ($project->bids()->count() > 0) {
            return false;
        }

        // فقط صاحب العمل يمكنه حذف مشروعه
        return $user->id === $project->employer_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
         // المدير العام أو صاحب العمل
        return $user->isAdmin() || $user->id === $project->employer_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
         // فقط المدير العام
        return $user->isAdmin();
    }

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    // صلاحيات مخصصة للمنصة
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

    /**
     * تحديد ما إذا كان يمكن للمستخدم تقديم عرض على المشروع
     */
    public function bid(User $user, Project $project): bool
    {
        // لا يمكن تقديم عرض إذا كان المشروع مغلق
        if (!$project->isOpen()) {
            return false;
        }

        // لا يمكن لصاحب العمل التقديم على مشروعه
        if ($user->id === $project->employer_id) {
            return false;
        }

        // يجب أن يكون مستقلاً
        if (!$user->isFreelancer()) {
            return false;
        }

        // المستخدم المحظور لا يمكنه التقديم
        if ($user->is_banned || !$user->is_active) {
            return false;
        }

        // لا يمكن تقديم أكثر من عرض لنفس المشروع
        if ($project->bids()->where('freelancer_id', $user->id)->exists()) {
            return false;
        }

        // لا يمكن التقديم إذا كان هناك عقد نشط
        if ($project->hasContract()) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض العروض على مشروعه
     */
    public function viewBids(User $user, Project $project): bool
    {
        // المدير العام أو صاحب العمل فقط
        return $user->isAdmin() || $user->id === $project->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم قبول عرض
     */
    public function acceptBid(User $user, Project $project): bool
    {
        // يجب أن يملك صلاحية عرض العروض أولاً
        if (!$this->viewBids($user, $project)) {
            return false;
        }

        // لا يمكن قبول عرض إذا كان المشروع مغلق
        if (!$project->isOpen()) {
            return false;
        }

        // لا يمكن قبول عرض إذا كان هناك عقد بالفعل
        if ($project->hasContract()) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم رفض عرض
     */
    public function rejectBid(User $user, Project $project): bool
    {
        return $this->acceptBid($user, $project);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إلغاء المشروع
     */
    public function cancel(User $user, Project $project): bool
    {
        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // لا يمكن إلغاء مشروع به عقد نشط
        if ($project->contract && $project->contract->isActive()) {
            return false;
        }

        // صاحب العمل يمكنه إلغاء مشروعه
        return $user->id === $project->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تمييز المشروع (Featured)
     */
    public function feature(User $user, Project $project): bool
    {
        // فقط المدير العام
        return $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إخفاء/إظهار المشروع
     */
    public function hide(User $user, Project $project): bool
    {
        // المدير العام أو صاحب العمل
        return $user->isAdmin() || $user->id === $project->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تمديد المشروع
     */
    public function extend(User $user, Project $project): bool
    {
        // فقط صاحب العمل والمدير العام
        return $user->isAdmin() || $user->id === $project->employer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم أرشفة المشروع
     */
    public function archive(User $user, Project $project): bool
    {
        // المدير العام أو صاحب العمل (فقط إذا كان مكتمل أو ملغى)
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id !== $project->employer_id) {
            return false;
        }

        return in_array($project->status, ['completed', 'cancelled']);
    }
}
