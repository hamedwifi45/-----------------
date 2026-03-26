<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
         // الإدارة يمكنها عرض قائمة المستخدمين
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('view_users')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if (!$user) {
            return false;
        }

        // المدير العام يرى الجميع
        if ($user->isAdmin()) {
            return true;
        }

        // المستخدم يمكنه رؤية ملفه الشخصي
        if ($user->id === $model->id) {
            return true;
        }

        // الملفات الشخصية العامة مرئية للجميع
        if ($model->profile && $model->profile->is_public) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
         // المدير العام يمكنه تعديل الجميع
        if ($user->isAdmin()) {
            return true;
        }

        // المستخدم يمكنه تعديل ملفه فقط
        if ($user->id === $model->id) {
            return true;
        }

        // إدارة الملفات الشخصية
        if ($user->hasPermission('edit_users')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->isAdmin()) {
            // لا يمكن حذف نفسك
            if ($user->id === $model->id) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
    /**
     * تحديد ما إذا كان يمكن للمستخدم حظر مستخدم آخر
     */
    public function ban(User $user, User $target): bool
    {
        // فقط الإدارة
        if ($user->hasPermission('ban_users')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إلغاء حظر مستخدم
     */
    public function unban(User $user, User $target): bool
    {
        // نفس شروط الحظر
        return $this->ban($user, $target);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تفعيل مستخدم
     */
    public function activate(User $user, User $target): bool
    {
        // فقط الإدارة
        return $user->hasPermission('activate_users');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعطيل مستخدم
     */
    public function deactivate(User $user, User $target): bool
    {
        // فقط الإدارة
        return $user->hasPermission('deactivate_users');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم توثيق مستخدم
     */
    public function verify(User $user, User $target): bool
    {
        // فقط إدارة التوثيق
        if ($user->hasPermission('verify_users')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إلغاء توثيق مستخدم
     */
    public function unverify(User $user, User $target): bool
    {
        // نفس شروط التوثيق
        return $this->verify($user, $target);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل دور مستخدم
     */
    public function updateRole(User $user, User $target): bool
    {
        // فقط المدير العام
        return $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعيين أدوار لمستخدم
     */
    public function assignRoles(User $user, User $target): bool
    {
        // فقط الإدارة العليا
        if ($user->hasPermission('assign_roles')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض سجل نشاط المستخدم
     */
    public function viewActivityLog(User $user, User $target): bool
    {
        // فقط الإدارة
        return $user->hasPermission('view_user_activity');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض معاملات المستخدم المالية
     */
    public function viewFinancials(User $user, User $target): bool
    {
        // فقط الإدارة المالية
        return $user->hasPermission('view_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض رسائل المستخدم
     */
    public function viewMessages(User $user, User $target): bool
    {
        // فقط الإدارة الأمنية
        return $user->hasPermission('view_user_messages');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل ملفه الشخصي
     */
    public function updateOwnProfile(User $user, User $target): bool
    {
        // المستخدم يمكنه تعديل ملفه فقط
        return $user->id === $target->id && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تغيير كلمة المرور
     */
    public function changePassword(User $user, User $target): bool
    {
        // المستخدم يمكنه تغيير كلمة مروره فقط
        return $user->id === $target->id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إعادة تعيين كلمة مرور مستخدم آخر
     */
    public function resetPassword(User $user, User $target): bool
    {
        // فقط الإدارة
        return $user->hasPermission('reset_passwords');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض معلومات حساسة
     */
    public function viewSensitiveInfo(User $user, User $target): bool
    {
        // فقط الإدارة العليا
        return $user->hasPermission('view_sensitive_data');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير بيانات المستخدم
     */
    public function exportData(User $user, User $target): bool
    {
        // المستخدم يمكنه تصدير بياناته فقط (GDPR)
        if ($user->id === $target->id) {
            return true;
        }

        // الإدارة يمكنها التصدير
        if ($user->hasPermission('export_user_data')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف حسابه
     */
    public function deleteOwnAccount(User $user, User $target): bool
    {
        // المستخدم يمكنه حذف حسابه (مع شروط)
        if ($user->id !== $target->id) {
            return false;
        }

        // لا يمكن الحذف إذا كان هناك عقود نشطة
        if ($user->freelancerContracts()->where('status', 'active')->exists()) {
            return false;
        }

        if ($user->employerContracts()->where('status', 'active')->exists()) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم ترقية مستخدم آخر
     */
    public function promote(User $user, User $target): bool
    {
        // فقط المدير العام
        return $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم خفض رتبة مستخدم آخر
     */
    public function demote(User $user, User $target): bool
    {
        // فقط المدير العام
        return $user->isAdmin();
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض عناوين IP للمستخدم
     */
    public function viewIpAddresses(User $user, User $target): bool
    {
        // فقط الإدارة الأمنية
        return $user->hasPermission('view_security_logs');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تسجيل دخول كمستخدم (Impersonate)
     */
    public function impersonate(User $user, User $target): bool
    {
        // فقط المدير العام (ولا يمكنه تسجيل الدخول كمدير آخر)
        if ($user->isAdmin() && !$target->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إنهاء جلسات المستخدم الأخرى
     */
    public function terminateSessions(User $user, User $target): bool
    {
        // المستخدم يمكنه إنهاء جلساته فقط
        if ($user->id === $target->id) {
            return true;
        }

        // الإدارة يمكنها إنهاء جلسات الآخرين
        return $user->hasPermission('terminate_sessions');
    }
}
