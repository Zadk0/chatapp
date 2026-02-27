<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'status',
        'last_seen',
        'is_online',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen' => 'datetime',
            'password' => 'hashed', // Bcrypt automático
            'is_online' => 'boolean',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Avatar con iniciales
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=25D366&color=fff';
    }

    public function getUnreadCountFor(Conversation $conversation): int
    {
        $pivot = $this->conversations()
            ->where('conversations.id', $conversation->id)
            ->first()?->pivot;

        if (!$pivot || !$pivot->last_read_at) {
            return $conversation->messages()->count();
        }

        return $conversation->messages()
            ->where('created_at', '>', $pivot->last_read_at)
            ->where('sender_id', '!=', $this->id)
            ->count();
    }
}
