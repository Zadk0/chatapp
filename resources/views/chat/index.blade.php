@extends('layouts.app')
@section('title', 'ChatApp')

@section('styles')
<style>
/* ─── LAYOUT ─────────────────────────────────────────────────────────────── */
.app-wrapper {
    display: flex;
    height: 100vh;
    overflow: hidden;
    background: #111b21;
}

/* ─── SIDEBAR ────────────────────────────────────────────────────────────── */
.sidebar {
    width: 360px;
    min-width: 280px;
    background: #111b21;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #2a3942;
}
@media (max-width: 768px) {
    .sidebar { width: 100%; }
    .chat-panel { display: none; }
    .sidebar.hidden { display: none; }
    .chat-panel.active { display: flex; width: 100%; }
}

/* Sidebar Header */
.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    background: #202c33;
}
.sidebar-header .user-info { display: flex; align-items: center; gap: .75rem; }
.sidebar-header .avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; }
.sidebar-header .user-name { color: #e9edef; font-weight: 600; font-size: .95rem; }
.sidebar-header .user-status { color: #8696a0; font-size: .75rem; }
.sidebar-header .actions { display: flex; gap: .5rem; }
.icon-btn {
    background: none;
    border: none;
    color: #aebac1;
    cursor: pointer;
    padding: .4rem;
    border-radius: 50%;
    font-size: 1.2rem;
    transition: background .2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.icon-btn:hover { background: #2a3942; color: #e9edef; }

/* Search */
.search-box {
    padding: .75rem 1rem;
    background: #111b21;
}
.search-input-wrap { position: relative; }
.search-input-wrap input {
    width: 100%;
    padding: .6rem 1rem .6rem 2.5rem;
    background: #202c33;
    border: none;
    border-radius: 8px;
    color: #e9edef;
    font-size: .9rem;
    outline: none;
}
.search-input-wrap input::placeholder { color: #8696a0; }
.search-icon {
    position: absolute;
    left: .75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #8696a0;
    pointer-events: none;
}

/* New Chat Button */
.new-chat-btn {
    margin: 0 1rem .75rem;
    padding: .65rem 1rem;
    background: #00a884;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
}
.new-chat-btn:hover { background: #008f72; }

/* Conversations List */
.conversations-list { flex: 1; overflow-y: auto; }
.conversations-list::-webkit-scrollbar { width: 4px; }
.conversations-list::-webkit-scrollbar-thumb { background: #374045; border-radius: 2px; }

.conv-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .75rem 1.25rem;
    cursor: pointer;
    transition: background .15s;
    border-bottom: 1px solid #1f2c33;
    text-decoration: none;
}
.conv-item:hover, .conv-item.active { background: #2a3942; }
.conv-item .avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
.conv-item .info { flex: 1; overflow: hidden; }
.conv-item .top-row { display: flex; justify-content: space-between; align-items: center; }
.conv-item .name { color: #e9edef; font-weight: 600; font-size: .95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.conv-item .time { color: #8696a0; font-size: .75rem; flex-shrink: 0; }
.conv-item .bottom-row { display: flex; justify-content: space-between; align-items: center; margin-top: .15rem; }
.conv-item .last-msg { color: #8696a0; font-size: .85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.conv-item .badge {
    background: #00a884;
    color: white;
    font-size: .7rem;
    font-weight: 700;
    border-radius: 50%;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 0 4px;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #8696a0;
}
.empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }

/* ─── CHAT PANEL ─────────────────────────────────────────────────────────── */
.chat-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #0b141a;
}

/* Chat Header */
.chat-header {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .75rem 1.25rem;
    background: #202c33;
    box-shadow: 0 1px 3px rgba(0,0,0,.3);
}
.chat-header .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.chat-header .info { flex: 1; }
.chat-header .name { color: #e9edef; font-weight: 600; font-size: .95rem; }
.chat-header .status { color: #8696a0; font-size: .78rem; }

/* Messages Area */
.messages-area {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 8%;
    display: flex;
    flex-direction: column;
    gap: .4rem;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23182229' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.messages-area::-webkit-scrollbar { width: 4px; }
.messages-area::-webkit-scrollbar-thumb { background: #374045; border-radius: 2px; }

.msg-wrapper {
    display: flex;
    align-items: flex-end;
    gap: .5rem;
    max-width: 65%;
}
.msg-wrapper.mine { flex-direction: row-reverse; align-self: flex-end; }
.msg-wrapper.theirs { align-self: flex-start; }

.msg-bubble {
    padding: .55rem .9rem;
    border-radius: 7.5px;
    font-size: .9rem;
    line-height: 1.5;
    word-break: break-word;
    position: relative;
    box-shadow: 0 1px 2px rgba(0,0,0,.3);
}
.msg-wrapper.mine .msg-bubble {
    background: #005c4b;
    color: #e9edef;
    border-bottom-right-radius: 2px;
}
.msg-wrapper.theirs .msg-bubble {
    background: #202c33;
    color: #e9edef;
    border-bottom-left-radius: 2px;
}
.msg-time {
    font-size: .68rem;
    color: #8696a0;
    margin-top: .2rem;
    display: block;
    text-align: right;
}
.msg-wrapper.mine .msg-time::after { content: ' ✓✓'; color: #53bdeb; }

.date-separator {
    text-align: center;
    margin: 1rem 0;
}
.date-separator span {
    background: #182229;
    color: #8696a0;
    font-size: .75rem;
    padding: .35rem .9rem;
    border-radius: 12px;
}

/* Input Area */
.input-area {
    padding: .75rem 1rem;
    background: #202c33;
    display: flex;
    align-items: center;
    gap: .75rem;
}
.msg-input {
    flex: 1;
    background: #2a3942;
    border: none;
    border-radius: 24px;
    padding: .75rem 1.25rem;
    color: #e9edef;
    font-size: .95rem;
    outline: none;
    resize: none;
    max-height: 100px;
    line-height: 1.4;
}
.msg-input::placeholder { color: #8696a0; }
.send-btn {
    width: 48px;
    height: 48px;
    background: #00a884;
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .2s, transform .15s;
    flex-shrink: 0;
}
.send-btn:hover { background: #008f72; transform: scale(1.05); }
.send-btn:disabled { background: #374045; cursor: not-allowed; transform: none; }

/* Welcome Screen */
.welcome-screen {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    color: #8696a0;
    text-align: center;
    padding: 2rem;
    gap: 1rem;
}
.welcome-screen .big-icon { font-size: 6rem; opacity: .6; }
.welcome-screen h2 { color: #e9edef; font-size: 1.8rem; font-weight: 300; }
.welcome-screen p { font-size: .9rem; max-width: 360px; line-height: 1.6; }
.secure-badge {
    display: flex;
    align-items: center;
    gap: .4rem;
    font-size: .8rem;
    margin-top: 1rem;
    border: 1px solid #2a3942;
    padding: .4rem .9rem;
    border-radius: 20px;
}

/* ─── MODAL ──────────────────────────────────────────────────────────────── */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.6);
    z-index: 100;
    align-items: center;
    justify-content: center;
}
.modal-overlay.open { display: flex; }
.modal {
    background: #202c33;
    border-radius: 12px;
    padding: 1.5rem;
    width: 90%;
    max-width: 400px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
}
.modal h3 { color: #e9edef; font-size: 1.1rem; margin-bottom: 1rem; }
.modal .search-wrap { position: relative; margin-bottom: .75rem; }
.modal .search-wrap input {
    width: 100%;
    padding: .6rem 1rem .6rem 2.5rem;
    background: #2a3942;
    border: none;
    border-radius: 8px;
    color: #e9edef;
    font-size: .9rem;
    outline: none;
}
.modal .search-wrap .si { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: #8696a0; }
.users-list { overflow-y: auto; flex: 1; }
.user-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .6rem .5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background .15s;
}
.user-item:hover { background: #2a3942; }
.user-item img { width: 42px; height: 42px; border-radius: 50%; }
.user-item .uname { color: #e9edef; font-size: .9rem; font-weight: 500; }
.user-item .ustatus { color: #8696a0; font-size: .8rem; }
.modal-close {
    background: none;
    border: none;
    color: #8696a0;
    cursor: pointer;
    float: right;
    font-size: 1.2rem;
    padding: 0;
}
.modal-close:hover { color: #e9edef; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
</style>
@endsection

@section('content')
<div class="app-wrapper">

    {{-- ─── SIDEBAR ─── --}}
    <div class="sidebar" id="sidebar">

        {{-- Header --}}
        <div class="sidebar-header">
            <div class="user-info">
                <img src="{{ auth()->user()->avatar_url }}" alt="" class="avatar">
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-status">En línea</div>
                </div>
            </div>
            <div class="actions">
                <button class="icon-btn" title="Nuevo chat" onclick="openModal()">✏️</button>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="icon-btn" title="Cerrar sesión">⏻</button>
                </form>
            </div>
        </div>

        {{-- Search --}}
        <div class="search-box">
            <div class="search-input-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Buscar conversación..." id="searchConv" oninput="filterConversations(this.value)">
            </div>
        </div>

        {{-- New Chat --}}
        <button class="new-chat-btn" onclick="openModal()">
            💬 Nueva conversación
        </button>

        {{-- Conversations --}}
        <div class="conversations-list" id="convList">
            @forelse($conversations as $conv)
                @php
                    $last = $conv->lastMessage->first();
                    $unread = auth()->user()->getUnreadCountFor($conv);
                @endphp
                <a
                    class="conv-item {{ isset($conversation) && $conversation->id === $conv->id ? 'active' : '' }}"
                    href="{{ route('chat.show', $conv) }}"
                    data-name="{{ strtolower($conv->getNameFor(auth()->user())) }}"
                >
                    <img src="{{ $conv->getAvatarFor(auth()->user()) }}" alt="" class="avatar">
                    <div class="info">
                        <div class="top-row">
                            <span class="name">{{ $conv->getNameFor(auth()->user()) }}</span>
                            @if($last)
                                <span class="time">{{ $last->created_at->format('H:i') }}</span>
                            @endif
                        </div>
                        <div class="bottom-row">
                            <span class="last-msg">
                                @if($last)
                                    {{ $last->is_deleted ? '🚫 Mensaje eliminado' : Str::limit($last->content, 35) }}
                                @else
                                    Inicia la conversación...
                                @endif
                            </span>
                            @if($unread > 0)
                                <span class="badge">{{ $unread }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <div class="icon">💬</div>
                    <p>No tienes conversaciones aún.<br>¡Inicia una nueva!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ─── CHAT PANEL ─── --}}
    <div class="chat-panel">
        @isset($conversation)
            {{-- Chat Header --}}
            @php $other = $conversation->getOtherParticipant(auth()->user()); @endphp
            <div class="chat-header">
                <img src="{{ $conversation->getAvatarFor(auth()->user()) }}" class="avatar" alt="">
                <div class="info">
                    <div class="name">{{ $conversation->getNameFor(auth()->user()) }}</div>
                    <div class="status" id="otherStatus">
                        {{ $other?->is_online ? '🟢 En línea' : ($other?->last_seen ? 'Visto ' . $other->last_seen->diffForHumans() : 'Sin conexión') }}
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            <div class="messages-area" id="messagesArea">
                @php $lastDate = null; @endphp
                @foreach($messages as $msg)
                    @php $msgDate = $msg->created_at->format('d/m/Y'); @endphp
                    @if($msgDate !== $lastDate)
                        <div class="date-separator">
                            <span>{{ $msg->created_at->isToday() ? 'Hoy' : ($msg->created_at->isYesterday() ? 'Ayer' : $msgDate) }}</span>
                        </div>
                        @php $lastDate = $msgDate; @endphp
                    @endif
                    <div class="msg-wrapper {{ $msg->isFromUser(auth()->user()) ? 'mine' : 'theirs' }}">
                        <div class="msg-bubble">
                            {{ $msg->display_content }}
                            <span class="msg-time">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Input --}}
            <div class="input-area">
                <textarea
                    class="msg-input"
                    id="msgInput"
                    placeholder="Escribe un mensaje..."
                    rows="1"
                    onkeydown="handleKeydown(event)"
                    oninput="autoResize(this)"
                ></textarea>
                <button class="send-btn" id="sendBtn" onclick="sendMessage()">➤</button>
            </div>

        @else
            {{-- Welcome Screen --}}
            <div class="welcome-screen">
                <div class="big-icon">💬</div>
                <h2>ChatApp</h2>
                <p>Selecciona una conversación para comenzar a chatear o inicia una nueva.</p>
                <div class="secure-badge">
                    🔒 Mensajes cifrados con AES-256
                </div>
            </div>
        @endisset
    </div>
</div>

{{-- ─── MODAL NUEVO CHAT ─── --}}
<div class="modal-overlay" id="newChatModal">
    <div class="modal">
        <div class="modal-header">
            <h3>💬 Nueva conversación</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="search-wrap">
            <span class="si">🔍</span>
            <input type="text" placeholder="Buscar usuario..." oninput="filterUsers(this.value)" id="userSearch">
        </div>
        <div class="users-list" id="usersList">
            @foreach($users as $u)
                <form method="POST" action="{{ route('conversation.create') }}" class="user-form" data-name="{{ strtolower($u->name) }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $u->id }}">
                    <div class="user-item" onclick="this.closest('form').submit()">
                        <img src="{{ $u->avatar_url }}" alt="">
                        <div>
                            <div class="uname">{{ $u->name }}</div>
                            <div class="ustatus">{{ $u->is_online ? '🟢 En línea' : 'Sin conexión' }}</div>
                        </div>
                    </div>
                </form>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CONV_ID = {{ isset($conversation) ? $conversation->id : 'null' }};
const CURRENT_USER_ID = {{ auth()->id() }};
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ─── SCROLL AL FONDO ──────────────────────────────────────────────────────
function scrollToBottom() {
    const area = document.getElementById('messagesArea');
    if (area) area.scrollTop = area.scrollHeight;
}
scrollToBottom();

// ─── AUTO RESIZE TEXTAREA ────────────────────────────────────────────────
function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
}

// ─── ENVIAR CON ENTER ────────────────────────────────────────────────────
function handleKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

// ─── ENVIAR MENSAJE ──────────────────────────────────────────────────────
async function sendMessage() {
    if (!CONV_ID) return;
    const input = document.getElementById('msgInput');
    const content = input.value.trim();
    if (!content) return;

    const btn = document.getElementById('sendBtn');
    btn.disabled = true;
    input.value = '';
    input.style.height = 'auto';

    // Optimistic UI
    appendMessage({ content, sender_id: CURRENT_USER_ID, created_at: now() }, true);
    scrollToBottom();

    try {
        const res = await fetch(`/chat/${CONV_ID}/message`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ content }),
        });
        const data = await res.json();
        if (!data.success) throw new Error('Error al enviar');
    } catch (err) {
        console.error(err);
        alert('Error al enviar el mensaje. Inténtalo de nuevo.');
    } finally {
        btn.disabled = false;
        input.focus();
    }
}

// ─── AGREGAR MENSAJE AL DOM ──────────────────────────────────────────────
function appendMessage(msg, isMine) {
    const area = document.getElementById('messagesArea');
    if (!area) return;
    const wrapper = document.createElement('div');
    wrapper.className = `msg-wrapper ${isMine ? 'mine' : 'theirs'}`;
    wrapper.innerHTML = `
        <div class="msg-bubble">
            ${escapeHtml(msg.content)}
            <span class="msg-time">${msg.created_at}</span>
        </div>`;
    area.appendChild(wrapper);
}

function escapeHtml(text) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(text));
    return d.innerHTML;
}

function now() {
    return new Date().toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' });
}

// ─── FILTRAR CONVERSACIONES ──────────────────────────────────────────────
function filterConversations(q) {
    document.querySelectorAll('.conv-item').forEach(el => {
        el.style.display = el.dataset.name?.includes(q.toLowerCase()) ? '' : 'none';
    });
}

// ─── MODAL ────────────────────────────────────────────────────────────────
function openModal() {
    document.getElementById('newChatModal').classList.add('open');
    document.getElementById('userSearch')?.focus();
}
function closeModal() {
    document.getElementById('newChatModal').classList.remove('open');
}
document.getElementById('newChatModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
function filterUsers(q) {
    document.querySelectorAll('.user-form').forEach(f => {
        f.style.display = f.dataset.name?.includes(q.toLowerCase()) ? '' : 'none';
    });
}

// ─── WEBSOCKETS (Laravel Reverb) ──────────────────────────────────────────
@if(isset($conversation) && config('broadcasting.default') === 'reverb')
// Conectar via WebSocket cuando Reverb esté configurado
if (typeof window.Echo !== 'undefined' && CONV_ID) {
    window.Echo.join(`conversation.${CONV_ID}`)
        .here(members => {
            console.log('Miembros en sala:', members.length);
        })
        .listen('.message.sent', (data) => {
            if (data.sender_id !== CURRENT_USER_ID) {
                appendMessage({
                    content: data.content,
                    sender_id: data.sender_id,
                    created_at: data.created_at,
                }, false);
                scrollToBottom();
            }
        });
}
@endif

// Polling como fallback (cada 3s)
@if(isset($conversation))
let lastMsgId = {{ $messages->last()?->id ?? 0 }};
setInterval(async () => {
    if (!CONV_ID) return;
    try {
        const res = await fetch(`/chat/${CONV_ID}?after=${lastMsgId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) return;
        const data = await res.json();
        if (data.messages?.length) {
            data.messages.forEach(msg => {
                if (msg.sender_id !== CURRENT_USER_ID) {
                    appendMessage(msg, false);
                }
                lastMsgId = Math.max(lastMsgId, msg.id);
            });
            scrollToBottom();
        }
    } catch {}
}, 3000);
@endif
</script>
@endsection
