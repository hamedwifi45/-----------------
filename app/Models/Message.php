<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
   use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'sender_id',
        'receiver_id',
        'parent_message_id',
        'body',
        'is_read',
        'is_deleted_by_sender',
        'is_deleted_by_receiver',
        'type',
        'read_at',
        'sender_ip',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_deleted_by_sender' => 'boolean',
        'is_deleted_by_receiver' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_message_id');
    }

    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'parent_message_id');
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }

    public function disputeEvidence(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DisputeEvidence::class);
    }

    public function scopeUnread($query, int $userId)
    {
        return $query->where('receiver_id', $userId)
            ->where('is_read', false);
    }

    public function scopeContract($query, int $contractId)
    {
        return $query->where('contract_id', $contractId);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
