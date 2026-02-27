<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_group',
        'avatar',
        'created_by',
    ];

    protected $casts = [
        'is_group' => 'boolean',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest()->limit(1);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function getOtherParticipant(User $currentUser): ?User
    {
        return $this->participants
            ->where('id', '!=', $currentUser->id)
            ->first();
    }

    public function getNameFor(User $currentUser): string
    {
        if ($this->is_group) {
            return $this->name ?? 'Grupo';
        }
        $other = $this->getOtherParticipant($currentUser);
        return $other?->name ?? 'Usuario desconocido';
    }

    public function getAvatarFor(User $currentUser): string
    {
        if ($this->is_group) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'G') . '&background=128C7E&color=fff';
        }
        $other = $this->getOtherParticipant($currentUser);
        return $other?->avatar_url ?? 'https://ui-avatars.com/api/?name=U&background=25D366&color=fff';
    }
}
