<?php

namespace App\Policies;

use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PortfolioItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PortfolioItem $item): bool
    {
         // إذا كان منشوراً وغير سري، الجميع يمكنه رؤيته
        if ($item->is_published && !$item->is_confidential) {
            return true;
        }

        // إذا لم يكن المستخدم مسجلاً، لا يمكنه رؤية الأعمال الخاصة
        if (!$user) {
            return false;
        }

        // صاحب العمل يمكنه رؤية أعماله
        if ($user->id === $item->profile->user_id) {
            return true;
        }

        // الإدارة يمكنها رؤية كل شيء
        if ($user->isAdmin()) {
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

        // يجب أن يكون لديه ملف شخصي
        if (!$user->profile) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PortfolioItem $item): bool
    {
         // الإدارة
        if ($user->isAdmin()) {
            return true;
        }

        // صاحب الملف الشخصي فقط
        return $user->id === $item->profile->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PortfolioItem $item): bool
    {
        // الإدارة
        if ($user->isAdmin()) {
            return true;
        }

        // صاحب الملف الشخصي فقط
        return $user->id === $item->profile->user_id;  
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PortfolioItem $item): bool
    {
        return $user->id === $item->profile->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PortfolioItem $item): bool
    {
         // فقط الإدارة
        return $user->isAdmin();
    }
    /**
     * تحديد ما إذا كان يمكن للمستخدم نشر عمل
     */
    public function publish(User $user, PortfolioItem $item): bool
    {
        // صاحب الملف الشخصي أو الإدارة
        return $user->id === $item->profile->user_id || $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إخفاء عمل
     */
    public function hide(User $user, PortfolioItem $item): bool
    {
        // نفس شروط النشر
        return $this->publish($user, $item);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تمييز عمل كمميز
     */
    public function feature(User $user, PortfolioItem $item): bool
    {
        // فقط الإدارة
        return $user->hasPermission('feature_portfolio');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديد عمل كسري
     */
    public function markAsConfidential(User $user, PortfolioItem $item): bool
    {
        // صاحب الملف الشخصي فقط
        return $user->id === $item->profile->user_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إضافة صور للعمل
     */
    public function addImages(User $user, PortfolioItem $item): bool
    {
        // نفس شروط التعديل
        return $this->update($user, $item);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف صورة من العمل
     */
    public function deleteImage(User $user, PortfolioItem $item): bool
    {
        // نفس شروط التعديل
        return $this->update($user, $item);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعيين صورة كغلاف
     */
    public function setCoverImage(User $user, PortfolioItem $item): bool
    {
        // نفس شروط التعديل
        return $this->update($user, $item);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض إحصائيات العمل
     */
    public function viewStats(User $user, PortfolioItem $item): bool
    {
        // صاحب العمل أو الإدارة
        if ($user->id === $item->profile->user_id) {
            return true;
        }

        if ($user->hasPermission('view_analytics')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الإعجاب بالعمل
     */
    public function like(User $user, PortfolioItem $item): bool
    {
        // أي مستخدم نشط
        return $user && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إزالة الإعجاب
     */
    public function unlike(User $user, PortfolioItem $item): bool
    {
        // نفس شروط الإعجاب
        return $this->like($user, $item);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم مشاركة العمل
     */
    public function share(User $user, PortfolioItem $item): bool
    {
        // أي مستخدم نشط (للأعمال العامة)
        if ($item->is_published && !$item->is_confidential) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الإبلاغ عن عمل مخالف
     */
    public function report(User $user, PortfolioItem $item): bool
    {
        // أي مستخدم نشط
        return $user && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم (مدير) التعامل مع بلاغ
     */
    public function handleReport(User $user, PortfolioItem $item): bool
    {
        // فقط الإدارة
        return $user->hasPermission('manage_reports');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير العمل
     */
    public function export(User $user, PortfolioItem $item): bool
    {
        // صاحب العمل أو الإدارة
        return $user->id === $item->profile->user_id || $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم نسخ العمل
     */
    public function duplicate(User $user, PortfolioItem $item): bool
    {
        // صاحب العمل فقط
        return $user->id === $item->profile->user_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تغيير ترتيب الأعمال
     */
    public function reorder(User $user, PortfolioItem $item): bool
    {
        // صاحب الملف الشخصي
        return $user->id === $item->profile->user_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض التعليقات على العمل
     */
    public function viewComments(User $user, PortfolioItem $item): bool
    {
        // نفس شروط الرؤية العامة
        return $this->view($user, $item);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إضافة تعليق
     */
    public function comment(User $user, PortfolioItem $item): bool
    {
        // أي مستخدم نشط (للأعمال العامة)
        if ($item->is_published && !$item->is_confidential) {
            return $user && $user->is_active;
        }

        return false;
    }
}
