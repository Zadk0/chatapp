<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'type',
        'file_path',
        'read_at',
        'is_deleted',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    // ✅ CIFRADO: Los mensajes se cifran automáticamente en la base de datos
    public function setContentAttribute(string $value): void
    {
        $this->attributes['content'] = Crypt::encryptString($value);
    }

    public function getContentAttribute(?string $value): string
    {
        if (!$value) return '';
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return '[Mensaje cifrado - no se puede descifrar]';
        }
    }

    public function getDisplayContentAttribute(): string
    {
        if ($this->is_deleted) {
            return '🚫 Este mensaje fue eliminado';
        }
        return $this->content;
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function isFromUser(User $user): bool
    {
        return $this->sender_id === $user->id;
    }
}
