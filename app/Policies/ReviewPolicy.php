<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
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
    public function view(User $user, Review $review): bool
    {
        if($review->is_visible){
            return true;
        }
         // إذا كان مخفياً، فقط الإدارة والأطراف يمكنهم رؤيته
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $review->reviewer_id || $user->id === $review->reviewed_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
         if (!$user->is_active || $user->is_banned) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
         // لا يمكن تعديل التقييم بعد إنشائه (لحماية النزاهة)
        // يمكن فقط للإدارة تعديله في حالات استثنائية
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }
    public function createForContract(User $user, Review $review): bool
    {
        // يجب أن يكون المستخدم طرفاً في العقد
        if (!$review->contract) {
            return false;
        }

        if ($user->id !== $review->contract->employer_id && 
            $user->id !== $review->contract->freelancer_id) {
            return false;
        }

        // يجب أن يكون العقد مكتملاً
        if ($review->contract->status !== 'completed') {
            return false;
        }

        // لا يمكن التقييم إذا تم التقييم بالفعل
        if ($review->contract->reviews()->where('reviewer_id', $user->id)->exists()) {
            return false;
        }

        // يجب أن يكون خلال فترة محددة من الإكمال (مثلاً 30 يوم)
        if ($review->contract->completed_at && 
            $review->contract->completed_at->diffInDays(now()) > 30) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تقييم طرف آخر
     */
    public function reviewUser(User $user, Review $review): bool
    {
        // يجب أن يكون هناك عقد مشترك مكتمل
        if (!$review->contract) {
            return false;
        }

        // لا يمكن تقييم نفسك
        if ($user->id === $review->reviewed_id) {
            return false;
        }

        // يجب أن يكون المستخدم هو المقيّم المحدد
        if ($user->id !== $review->reviewer_id) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم رؤية التقييمات الخاصة
     */
    public function viewPrivateFeedback(User $user, Review $review): bool
    {
        // فقط الإدارة
        return $user->isAdmin() || $user->hasPermission('view_private_feedback');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الإبلاغ عن تقييم مخالف
     */
    public function report(User $user, Review $review): bool
    {
        // أي مستخدم نشط
        return $user && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم (مدير) التعامل مع بلاغ
     */
    public function handleReport(User $user, Review $review): bool
    {
        // فقط الإدارة
        return $user->hasPermission('manage_reports');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إخفاء تقييم
     */
    public function hide(User $user, Review $review): bool
    {
        // فقط الإدارة
        return $user->hasPermission('manage_reviews');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إظهار تقييم مخفي
     */
    public function show(User $user, Review $review): bool
    {
        // فقط الإدارة
        return $user->hasPermission('manage_reviews');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم التحقق من التقييم
     * (تأكيد أنه تقييم حقيقي)
     */
    public function verify(User $user, Review $review): bool
    {
        // فقط الإدارة
        return $user->hasPermission('verify_reviews');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الرد على التقييم
     */
    public function reply(User $user, Review $review): bool
    {
        // المقيّم عليه يمكنه الرد
        if ($user->id === $review->reviewed_id) {
            return true;
        }

        // الإدارة يمكنها الرد
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل تقييمه الخاص
     */
    public function updateOwnReview(User $user, Review $review): bool
    {
        // لا يمكن تعديل التقييمات (لحماية النزاهة)
        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف تقييمه الخاص
     */
    public function deleteOwnReview(User $user, Review $review): bool
    {
        // لا يمكن حذف التقييمات بعد إنشائها
        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض التقييمات لمستخدم معين
     */
    public function viewUserReviews(User $user, Review $review): bool
    {
        // التقييمات العامة مرئية للجميع
        if ($review->is_visible) {
            return true;
        }

        return $this->view($user, $review);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير التقييمات
     */
    public function export(User $user, Review $review): bool
    {
        // فقط الإدارة
        return $user->hasPermission('export_reviews');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم وضع تقييم كمميز
     */
    public function feature(User $user, Review $review): bool
    {
        // فقط الإدارة
        return $user->hasPermission('feature_reviews');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديد التقييم كغير مفيد
     */
    public function markAsUnhelpful(User $user, Review $review): bool
    {
        // أي مستخدم نشط (غير صاحب التقييم)
        return $user && $user->is_active && $user->id !== $review->reviewer_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديد التقييم كمفيد
     */
    public function markAsHelpful(User $user, Review $review): bool
    {
        // أي مستخدم نشط (غير صاحب التقييم)
        return $this->markAsUnhelpful($user, $review);
    }
}
