<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Profile;
use App\Services\Financial\WalletService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UserService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * إنشاء مستخدم جديد
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        
        try {
            // التحقق من عدم وجود البريد
            if (User::where('email', $data['email'])->exists()) {
                throw new Exception('Email already registered');
            }

            // إنشاء المستخدم
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'username' => $data['username'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? 'employer',
                'is_active' => true,
                'is_banned' => false,
            ]);

            // إنشاء الملف الشخصي
            Profile::create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'] ?? '',
                'last_name' => $data['last_name'] ?? '',
                'country' => $data['country'] ?? '',
                'is_complete' => false,
                'is_public' => true,
            ]);

            // إنشاء المحفظة
            $this->walletService->createWallet($user);

            DB::commit();

            Log::info('User created', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return $user;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * حظر مستخدم
     */
    public function banUser(User $user, User $admin, string $reason): User
    {
        $user->update([
            'is_banned' => true,
            'ban_reason' => $reason,
            'banned_at' => now(),
        ]);

        Log::warning('User banned', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'reason' => $reason,
        ]);

        return $user;
    }

    /**
     * إلغاء حظر مستخدم
     */
    public function unbanUser(User $user, User $admin): User
    {
        $user->update([
            'is_banned' => false,
            'ban_reason' => null,
            'banned_at' => null,
        ]);

        Log::info('User unbanned', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
        ]);

        return $user;
    }

    /**
     * توثيق مستخدم
     */
    public function verifyUser(User $user, User $admin): User
    {
        if ($user->profile) {
            $user->profile->update([
                'is_verified' => true,
                'verified_at' => now(),
            ]);
        }

        Log::info('User verified', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
        ]);

        return $user;
    }

    /**
     * الحصول على مستخدم بواسطة ID
     */
    public function getUserById(int $id): ?User
    {
        return User::with(['profile', 'wallet'])->find($id);
    }
}