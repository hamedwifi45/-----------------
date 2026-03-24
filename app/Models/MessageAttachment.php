<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    /** @use HasFactory<\Database\Factories\MessageAttachmentFactory> */
    use HasFactory;
    protected $fillable = [
        'message_id',
        'uploaded_by',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'file_extension',
        'file_size',
        'file_hash',
        'is_scanned',
        'is_malicious',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_scanned' => 'boolean',
        'is_malicious' => 'boolean',
    ];

    public function message(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function uploader(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeSafe($query)
    {
        return $query->where('is_scanned', true)
            ->where('is_malicious', false);
    }

    public function isSafe(): bool
    {
        return $this->is_scanned && !$this->is_malicious;
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
