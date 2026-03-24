<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    /** @use HasFactory<\Database\Factories\AuditLogFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'user_type',
        'action',
        'model_type',
        'model_id',
        'model_attribute',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
        'request_method',
        'request_url',
        'is_suspicious',
        'suspicion_reason',
    ];

    protected $casts = [
        'model_id' => 'integer',
        'old_values' => 'array',
        'new_values' => 'array',
        'is_suspicious' => 'boolean',
    ];

    /**
     *  منع التعديل نهائياً
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($auditLog) {
            throw new \Exception('Audit logs cannot be modified');
        });

        static::deleting(function ($auditLog) {
            throw new \Exception('Audit logs cannot be deleted');
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isSuspicious(): bool
    {
        return $this->is_suspicious === true;
    }
}
