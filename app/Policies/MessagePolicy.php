<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessagePolicy
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
    public function view(User $user, Message $message): bool
    {
        if (!$user) {
            return false;
        }

        // المدير العام
        if ($user->isAdmin()) {
            return true;
        }

        // مدير النزاعات (إذا كانت الرسالة جزء من نزاع)
        if ($user->hasPermission('view_disputes')) {
            if ($message->contract && $message->contract->disputes()->exists()) {
                return true;
            }
        }

        // المرسل
        if ($user->id === $message->sender_id) {
            return true;
        }

        // المستقبل
        if ($user->id === $message->receiver_id) {
            return true;
        }

        // أطراف العقد (لرسائل العقد)
        if ($message->contract) {
            if ($user->id === $message->contract->employer_id || 
                $user->id === $message->contract->freelancer_id) {
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
        if(!$user->is_active || $user->is_banned){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Message $message): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Message $message): bool
    {
         // يمكن حذف الرسالة فقط من جانب المستخدم (soft delete)
        if ($user->id === $message->sender_id) {
            return true;
        }

        if ($user->id === $message->receiver_id) {
            return true;
        }

        // الإدارة يمكنها الحذف الكامل
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Message $message): bool
    {
         if ($user->id === $message->sender_id || $user->id === $message->receiver_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Message $message): bool
    {
        return $user->isAdmin();
    }
    public function sendInContract(User $user, Message $message): bool
    {
        // يجب أن يكون المستخدم نشطاً
        if (!$user->is_active || $user->is_banned) {
            return false;
        }

        // يجب أن يكون طرفاً في العقد
        if (!$message->contract) {
            return false;
        }

        if ($user->id !== $message->contract->employer_id && 
            $user->id !== $message->contract->freelancer_id) {
            return false;
        }

        // لا يمكن الإرسال إذا كان العقد مغلقاً
        if (in_array($message->contract->status, ['completed', 'cancelled'])) {
            return false;
        }

        return true;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم قراءة الرسالة
     */
    public function read(User $user, Message $message): bool
    {
        // فقط المستقبل يمكنه تحديد الرسالة كمقروءة
        return $user->id === $message->receiver_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديد الرسالة كغير مقروءة
     */
    public function markAsUnread(User $user, Message $message): bool
    {
        // فقط المستقبل
        return $user->id === $message->receiver_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض حالة القراءة
     */
    public function viewReadStatus(User $user, Message $message): bool
    {
        // المرسل يمكنه رؤية إذا قُرئت رسالته
        if ($user->id === $message->sender_id) {
            return true;
        }

        // المستقبل يمكنه رؤية حالة قراءته
        if ($user->id === $message->receiver_id) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إرسال مرفق
     */
    public function sendAttachment(User $user, Message $message): bool
    {
        // نفس شروط إرسال الرسالة
        return $this->create($user);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض المرفقات
     */
    public function viewAttachments(User $user, Message $message): bool
    {
        // نفس شروط رؤية الرسالة
        return $this->view($user, $message);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف مرفق
     */
    public function deleteAttachment(User $user, Message $message): bool
    {
        // نفس شروط حذف الرسالة
        return $this->delete($user, $message);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض عنوان IP للمرسل
     */
    public function viewSenderIp(User $user, Message $message): bool
    {
        // فقط الإدارة (أمني)
        return $user->isAdmin() || $user->hasPermission('view_security_logs');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض رسائل عقد محدد
     */
    public function viewContractMessages(User $user, Message $message): bool
    {
        // إذا كان لديه صلاحية رؤية العقد
        if ($message->contract) {
            if ($user->id === $message->contract->employer_id || 
                $user->id === $message->contract->freelancer_id) {
                return true;
            }
        }

        return $this->view($user, $message);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم البحث في الرسائل
     */
    public function search(User $user, Message $message): bool
    {
        // أي مستخدم نشط يمكنه البحث في رسائله
        return $user && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تصدير المحادثات
     */
    public function export(User $user, Message $message): bool
    {
        // أطراف المحادثة فقط
        if ($user->id === $message->sender_id || $user->id === $message->receiver_id) {
            return true;
        }

        // الإدارة
        if ($user->hasPermission('export_messages')) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم الإبلاغ عن رسالة مخالفة
     */
    public function report(User $user, Message $message): bool
    {
        // أي مستخدم نشط
        return $user && $user->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم (مدير) التعامل مع بلاغ
     */
    public function handleReport(User $user, Message $message): bool
    {
        // فقط الإدارة
        return $user->hasPermission('manage_reports');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حظر المرسل
     */
    public function blockSender(User $user, Message $message): bool
    {
        // المستقبل يمكنه حظر المرسل
        return $user->id === $message->receiver_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض رسائل نظام
     */
    public function viewSystemMessages(User $user, Message $message): bool
    {
        // رسائل النظام متاحة لأطراف العقد فقط
        if ($message->type === 'system') {
            if ($message->contract) {
                if ($user->id === $message->contract->employer_id || 
                    $user->id === $message->contract->freelancer_id) {
                    return true;
                }
            }
        }

        return $this->view($user, $message);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم استخدام الرسائل في نزاع
     * (كأدلة)
     */
    public function useAsEvidence(User $user, Message $message): bool
    {
        // أطراف المحادثة يمكنهم استخدام الرسائل كأدلة
        if ($user->id === $message->sender_id || $user->id === $message->receiver_id) {
            return true;
        }

        return false;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم (مدير) عرض الرسائل في نزاع
     */
    public function viewInDispute(User $user, Message $message): bool
    {
        // إدارة النزاعات
        if ($user->hasPermission('manage_disputes')) {
            return true;
        }

        return $this->view($user, $message);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديد رسائل كمهمة
     */
    public function pin(User $user, Message $message): bool
    {
        // فقط أطراف المحادثة
        return $user->id === $message->sender_id || $user->id === $message->receiver_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم أرشفة المحادثة
     */
    public function archive(User $user, Message $message): bool
    {
        // فقط أطراف المحادثة
        return $user->id === $message->sender_id || $user->id === $message->receiver_id;
    }
}
