<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Auth\Access\Response;

class WalletPolicy
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
    public function view(User $user, Wallet $wallet): bool
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

        // فقط صاحب المحفظة
        return $user->id === $wallet->user_id;
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
    public function update(User $user, Wallet $wallet): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wallet $wallet): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Wallet $wallet): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Wallet $wallet): bool
    {
        return false;
    }
     // ==========================================
    // صلاحيات مالية حرجة ⚠️
    // ==========================================

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض الرصيد
     */
    public function viewBalance(User $user, Wallet $wallet): bool
    {
        // صاحب المحفظة أو الإدارة
        return $user->id === $wallet->user_id || 
               $user->isAdmin() || 
               $user->hasPermission('view_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إيداع أموال
     */
    public function deposit(User $user, Wallet $wallet): bool
    {
        // يجب أن يكون المستخدم نشطاً
        if (!$user->is_active || $user->is_banned) {
            return false;
        }

        // فقط صاحب المحفظة
        if ($user->id !== $wallet->user_id) {
            return false;
        }

        // يجب أن تكون المحفظة نشطة
        if (!$wallet->is_active) {
            return false;
        }

        // يجب أن لا تكون المحفظة مجمدة
        if ($wallet->isFrozen()) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم سحب أموال
     */
    public function withdraw(User $user, Wallet $wallet): bool
    {
        // يجب أن يكون المستخدم نشطاً
        if (!$user->is_active || $user->is_banned) {
            return false;
        }

        // فقط صاحب المحفظة
        if ($user->id !== $wallet->user_id) {
            return false;
        }

        // يجب أن تكون المحفظة نشطة
        if (!$wallet->is_active) {
            return false;
        }

        // يجب أن تكون المحفظة غير مجمدة
        if ($wallet->isFrozen()) {
            return false;
        }

        // يجب أن يكون مسموحاً بالسحب
        if (!$wallet->can_withdraw) {
            return false;
        }

        // يجب أن يكون المستخدم موثقاً (للسحب)
        if (!$user->profile || !$user->profile->is_verified) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحويل أموال
     * (تحويل داخلي بين المحافظ)
     */
    public function transfer(User $user, Wallet $wallet): bool
    {
        // نفس شروط السحب تقريباً
        return $this->withdraw($user, $wallet);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تجميد المحفظة
     * (فقط الإدارة)
     */
    public function freeze(User $user, Wallet $wallet): bool
    {
        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // المدير المالي
        if ($user->hasPermission('freeze_wallets')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إلغاء تجميد المحفظة
     */
    public function unfreeze(User $user, Wallet $wallet): bool
    {
        // نفس شروط التجميد
        return $this->freeze($user, $wallet);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل حدود السحب
     */
    public function updateLimits(User $user, Wallet $wallet): bool
    {
        // فقط الإدارة المالية
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('manage_financial_limits')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض سجل المعاملات
     */
    public function viewTransactions(User $user, Wallet $wallet): bool
    {
        // صاحب المحفظة أو الإدارة
        return $this->view($user, $wallet);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير المعاملات
     */
    public function exportTransactions(User $user, Wallet $wallet): bool
    {
        // صاحب المحفظة أو الإدارة المالية
        if ($user->id === $wallet->user_id) {
            return true;
        }

        if ($user->hasPermission('export_financial_data')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعديل ملاحظات الإدارة
     */
    public function updateAdminNotes(User $user, Wallet $wallet): bool
    {
        // فقط الإدارة
        return $user->isAdmin() || $user->hasPermission('manage_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تفعيل/تعطيل المحفظة
     */
    public function toggleActive(User $user, Wallet $wallet): bool
    {
        // فقط الإدارة المالية
        return $user->hasPermission('manage_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم السماح/منع السحب
     */
    public function toggleWithdrawal(User $user, Wallet $wallet): bool
    {
        // فقط الإدارة المالية
        return $user->hasPermission('manage_financials');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض الرصيد المعلق
     */
    public function viewPendingBalance(User $user, Wallet $wallet): bool
    {
        // نفس شروط عرض الرصيد
        return $this->viewBalance($user, $wallet);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض الرصيد المحجوز
     */
    public function viewReservedBalance(User $user, Wallet $wallet): bool
    {
        // نفس شروط عرض الرصيد
        return $this->viewBalance($user, $wallet);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم طلب تعديل على الرصيد
     * (في حال وجود خطأ)
     */
    public function requestBalanceAdjustment(User $user, Wallet $wallet): bool
    {
        // صاحب المحفظة فقط
        return $user->id === $wallet->user_id && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم (مدير) تعديل الرصيد
     */
    public function adjustBalance(User $user, Wallet $wallet): bool
    {
        // فقط المدير العام أو المالي مع صلاحية خاصة
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasPermission('adjust_balances')) {
            return true;
        }

        return false;
    }
}
