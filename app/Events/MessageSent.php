<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'content'         => $this->message->content, // Descifrado para el cliente
            'conversation_id' => $this->message->conversation_id,
            'sender_id'       => $this->message->sender_id,
            'sender' => [
                'id'         => $this->message->sender->id,
                'name'       => $this->message->sender->name,
                'avatar_url' => $this->message->sender->avatar_url,
            ],
            'created_at' => $this->message->created_at->format('H:i'),
        ];
    }
}
