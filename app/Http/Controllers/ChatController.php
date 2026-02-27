<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // ─── PÁGINA PRINCIPAL DEL CHAT ────────────────────────────────────────────
    public function index()
    {
        $user = Auth::user();

        $conversations = $user->conversations()
            ->with(['participants', 'lastMessage.sender'])
            ->orderByDesc(function ($query) {
                $query->select('created_at')
                    ->from('messages')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1);
            })
            ->get();

        $users = User::where('id', '!=', $user->id)
            ->where('email_verified_at', '!=', null)
            ->get();

        return view('chat.index', compact('conversations', 'users'));
    }

    // ─── VER CONVERSACIÓN ─────────────────────────────────────────────────────
    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        // Verificar que el usuario pertenece a la conversación
        abort_unless(
            $conversation->participants->contains($user->id),
            403,
            'No tienes acceso a esta conversación.'
        );

        $messages = $conversation->messages()
            ->with('sender')
            ->get();

        // Marcar como leído
        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        $conversations = $user->conversations()
            ->with(['participants', 'lastMessage.sender'])
            ->get();

        $users = User::where('id', '!=', $user->id)
            ->where('email_verified_at', '!=', null)
            ->get();

        return view('chat.index', compact('conversation', 'conversations', 'messages', 'users'));
    }

    // ─── ENVIAR MENSAJE ───────────────────────────────────────────────────────
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        abort_unless(
            $conversation->participants->contains($user->id),
            403
        );

        $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        // El mensaje se cifra automáticamente en el modelo
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'content'         => $request->content, // Se cifra via mutador
            'type'            => 'text',
        ]);

        $message->load('sender');

        // Emitir evento WebSocket
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => [
                'id'         => $message->id,
                'content'    => $message->content, // Se descifra via accessor
                'sender_id'  => $message->sender_id,
                'sender'     => [
                    'id'         => $message->sender->id,
                    'name'       => $message->sender->name,
                    'avatar_url' => $message->sender->avatar_url,
                ],
                'created_at' => $message->created_at->format('H:i'),
                'is_mine'    => true,
            ],
        ]);
    }

    // ─── CREAR CONVERSACIÓN ───────────────────────────────────────────────────
    public function createConversation(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $currentUser = Auth::user();
        $targetUser  = User::findOrFail($request->user_id);

        // Buscar conversación existente entre los dos usuarios
        $existingConversation = Conversation::where('is_group', false)
            ->whereHas('participants', fn($q) => $q->where('users.id', $currentUser->id))
            ->whereHas('participants', fn($q) => $q->where('users.id', $targetUser->id))
            ->first();

        if ($existingConversation) {
            return redirect()->route('chat.show', $existingConversation);
        }

        // Crear nueva conversación
        $conversation = Conversation::create([
            'is_group'   => false,
            'created_by' => $currentUser->id,
        ]);

        $conversation->participants()->attach([
            $currentUser->id => ['last_read_at' => now()],
            $targetUser->id  => ['last_read_at' => null],
        ]);

        return redirect()->route('chat.show', $conversation);
    }
}
