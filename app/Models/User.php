<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'phone',
        'role',
        'is_staff',
        'is_active',
        'is_banned',
        'ban_reason',
        'banned_at',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_staff' => 'boolean',
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
        ];
    }
    
    public function getIsBannedAttribute($value): bool
    {
        return $value === true || ($this->banned_at !== null && $this->banned_at->isPast() === false);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function wallet() 
    {
        return $this->hasOne(Wallet::class);
    }

    // مشاريع كصاحب عمل
    public function employerProjects()
    {
        return $this->hasMany(Project::class, 'employer_id');
    }

    // عروض كمستقل
    public function freelancerBids()
    {
        return $this->hasMany(Bid::class, 'freelancer_id');
    }

    // عقود كمستقل
    public function freelancerContracts()
    {
        return $this->hasMany(Contract::class, 'freelancer_id');
    }

    // عقود كصاحب عمل
    public function employerContracts()
    {
        return $this->hasMany(Contract::class, 'employer_id');
    }

    // رسائل مرسلة
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // رسائل مستقبلة
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // معاملات مالية
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }



    // طلبات سحب
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    // تقييمات received
    public function receivedReviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class, 'reviewed_id');
    }

    // تقييمات given
    public function givenReviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    // نزاعات
    public function disputes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Dispute::class, 'opened_by');
    }
    public function notifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\App\Models\Notification::class, 'notifiable');
    }

    /**
     * الإشعارات غير المقروءة
     */
    public function unreadNotifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\App\Models\Notification::class, 'notifiable')
            ->whereNull('read_at');
    }

    /**
     * عدد الإشعارات غير المقروءة
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }
    // أدوار الصلاحيات
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles');
    }

    // سجل التدقيق
    public function auditLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Scopes للاستعلامات الشائعة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    public function scopeStaff($query)
    {
        return $query->where('is_staff', true);
    }

    public function scopeFreelancers($query)
    {
        return $query->where('role', 'freelancer');
    }

    public function scopeEmployers($query)
    {
        return $query->where('role', 'employer');
    }

    /**
     * دوال مساعدة للصلاحيات
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission(string $permissionName): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions()->where('name', $permissionName)->exists()) {
                return true;
            }
        }
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('Super Admin');
    }

    public function isFreelancer(): bool
    {
        return $this->role === 'freelancer';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
