@extends('layouts.app')

@section('title', ($isLive ? 'Live Chat' : 'Chat History') . ' - ' . $bot->name)

@section('content')
<div class="space-y-4">
    <div class="flex items-center gap-4 animate-gentle">
        <a href="{{ route('bots.show', $bot) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-amber-800 dark:text-amber-200">{{ $isLive ? 'Live Chat 💬' : 'Chat History 📜' }}</h1>
            <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">{{ $isLive ? 'Real-time conversations with' : 'Browse past conversations with' }} {{ $bot->name }}</p>
        </div>
        @if($isLive)
        <div class="ml-auto flex items-center gap-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse-soft"></div>
            <span class="text-sm text-green-600 dark:text-green-400 font-medium">Live & Connected</span>
        </div>
        @endif
    </div>

    <div x-data="chatManager()" x-init="init()" class="card-warm overflow-hidden">
        <div class="flex flex-col lg:flex-row h-[calc(100vh-200px)] min-h-[500px]">
            <!-- Sidebar with sessions -->
            <div class="w-full lg:w-80 border-r border-amber-100 dark:border-gray-700 flex flex-col bg-amber-50/30 dark:bg-gray-800/30">
                <div class="p-4 border-b border-amber-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <h3 class="font-bold text-amber-800 dark:text-amber-200 flex items-center gap-2">
                        <i class="fas fa-comments text-amber-500"></i>
                        <span>{{ $isLive ? '💬 Live Chats' : '📜 Chat History' }}</span>
                    </h3>

                    @if(!$isLive)
                    <form action="{{ route('bots.clear-all-chats', $bot) }}" method="POST" class="mt-3" @submit.prevent="clearAllChats">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-amber-50 dark:bg-red-900/20 text-amber-600 dark:text-red-400 rounded-xl text-sm font-medium hover:bg-amber-100 dark:hover:bg-red-900/30 transition flex items-center justify-center gap-2">
                            <i class="fas fa-broom"></i>
                            <span>Tidy Up All</span>
                        </button>
                    </form>
                    @endif

                    <form method="GET" class="mt-3 space-y-2" @submit.prevent="applyDateFilter">
                        <input type="hidden" name="tab" value="{{ $isLive ? 'live-chat' : 'history' }}">
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-amber-400 text-sm"></i>
                            <select name="date_preset" x-model="datePreset" @change="applyDateFilter" class="w-full pl-10 pr-4 py-2 border border-amber-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-amber-800 dark:text-amber-200 text-sm focus:ring-2 focus:ring-amber-500">
                                <option value="">All Time</option>
                                <option value="today">Today so far</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="last_7">Last 7 days</option>
                                <option value="this_month">This month</option>
                                <option value="last_month">Last month</option>
                                <option value="last_30">Last 30 days</option>
                                <option value="custom">Pick a date...</option>
                            </select>
                        </div>

                        <input type="date" name="filter_date" x-model="filterDate" @change="applyDateFilter" x-show="datePreset === 'custom'" class="w-full px-4 py-2 border border-amber-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-amber-800 dark:text-amber-200 text-sm">
                    </form>
                </div>

                <div class="flex-1 overflow-y-auto p-2 space-y-1" id="sessions-list">
                    @forelse($sessions as $session)
                    <?php
                        $isActive = $selectedSession === $session->session_id;
                        $sessionInfo = parseSessionId($session->session_id, $bot);
                        $lastTime = \Carbon\Carbon::parse($session->last_time);
                    ?>
                    <a href="#"
                       data-session-id="{{ $session->session_id }}"
                       @click.prevent="selectSession('{{ $session->session_id }}')"
                       class="session-item block p-3 rounded-xl transition-all {{ $isActive ? 'gradient-warm text-amber-900 shadow-md' : 'hover:bg-amber-50 dark:hover:bg-gray-800' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">{{ $sessionInfo['icon'] ?? '💬' }}</span>
                                    <span class="font-mono text-xs truncate {{ $isActive ? 'text-amber-900' : 'text-amber-600 dark:text-amber-400' }}">
                                        {{ substr($session->session_id, 0, 20) }}...
                                    </span>
                                </div>
                                @if(isset($sessionInfo['channel_name']))
                                <p class="text-xs {{ $isActive ? 'text-amber-800/70' : 'text-amber-400' }} mt-1">
                                    <i class="fas fa-link mr-1"></i>{{ $sessionInfo['channel_name'] }}
                                </p>
                                @endif
                                <div class="flex items-center gap-3 mt-2 text-xs {{ $isActive ? 'text-amber-800/70' : 'text-amber-400' }}">
                                    <span class="session-last-time" data-session-time="{{ $session->session_id }}">
                                        <i class="far fa-clock mr-1"></i>{{ $lastTime->format('M d, H:i') }}
                                    </span>
                                    <span class="session-msg-count" data-session-count="{{ $session->session_id }}">
                                        <i class="fas fa-comment mr-1"></i>{{ $session->msgs }} msgs
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="p-8 text-center text-amber-400">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                        <p>No conversations yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat messages area -->
            <div class="flex-1 flex flex-col bg-white dark:bg-gray-800">
                @if($selectedSession)
                    <div class="p-4 border-b border-amber-100 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-wrap justify-between items-center gap-3 sticky top-0 z-10">
                        <div class="flex items-center gap-2">
                            <div class="gradient-warm rounded-full w-8 h-8 flex items-center justify-center">
                                <i class="fas fa-comment-dots text-amber-900 text-xs"></i>
                            </div>
                            <div>
                                <span class="font-mono text-sm text-amber-700 dark:text-amber-300">{{ substr($selectedSession, 0, 25) }}...</span>
                                @if(isset($sessionInfo) && isset($sessionInfo['channel_name']))
                                    <span class="ml-2 px-2 py-0.5 bg-amber-50 dark:bg-gray-700 rounded-full text-xs text-amber-600">{{ $sessionInfo['channel_name'] }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('bots.export-session', $bot) }}?session_id={{ urlencode($selectedSession) }}"
                            class="px-3 py-1.5 text-sm bg-amber-50 dark:bg-gray-700 hover:bg-amber-100 dark:hover:bg-gray-600 rounded-xl transition flex items-center gap-1 text-amber-600">
                                <i class="fas fa-download"></i>
                                <span>Save</span>
                            </a>
                            @if(!$isLive)
                            <button @click="clearSession('{{ $selectedSession }}')"
                                    class="px-3 py-1.5 text-sm bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-xl transition flex items-center gap-1">
                                <i class="fas fa-trash-alt"></i>
                                <span>Clear</span>
                            </button>
                            @endif
                        </div>
                    </div>

                    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-amber-50/30 to-white dark:from-gray-900 dark:to-gray-800">
                        @if($messages && count($messages) > 0)
                            @foreach($messages as $msg)
                                @include('chat.partials.message-bubble', ['message' => $msg])
                            @endforeach
                        @else
                            <div id="empty-state" class="flex-1 flex flex-col items-center justify-center h-full opacity-80">
                                <div class="text-6xl mb-4">💭</div>
                                <h3 class="text-xl font-semibold text-amber-700 dark:text-amber-300">Start the Conversation</h3>
                                <p class="text-amber-500 dark:text-amber-400 mt-2">Be the first to say hello!</p>
                            </div>
                        @endif
                    </div>

                    @if($isLive)
                    <div class="p-4 border-t border-amber-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <form id="reply-form" @submit.prevent="sendReply" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="session_id" value="{{ $selectedSession }}">
                            <div class="flex-1 relative">
                                <input type="text" name="message" id="reply-message" x-model="replyMessage"
                                    class="w-full px-4 py-3 pr-12 border border-amber-200 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-amber-500 focus:border-transparent bg-white dark:bg-gray-700 text-amber-800 dark:text-amber-200"
                                    placeholder="Type a warm reply..." autocomplete="off">
                                <button type="submit" id="reply-submit-btn" :disabled="!replyMessage.trim()"
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-1.5 btn-soft rounded-xl text-sm disabled:opacity-50">
                                    Send 💝
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                @else
                    <div class="flex-1 flex items-center justify-center">
                        <div class="text-center p-8">
                            <div class="text-6xl mb-4">🤗</div>
                            <h3 class="text-xl font-semibold text-amber-700 dark:text-amber-300">Welcome to the Chat</h3>
                            <p class="text-amber-500 dark:text-amber-400 mt-2">Choose a conversation from the sidebar to get started</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.6/dist/purify.min.js"></script>
<script>
function parseMarkdown(text) {
    if (typeof marked !== 'undefined') {
        const raw = marked.parse(text);
        return DOMPurify.sanitize(raw);
    }
    return escapeHtml(text);
}

function chatManager() {
    return {
        lastMsgId: {{ $messages && count($messages) > 0 ? ($messages->max('id') ?? 0) : 0 }},
        pollingInterval: null,
        isPolling: false,
        replyMessage: '',
        datePreset: '{{ $datePreset }}',
        filterDate: '{{ $filterDate }}',
        selectedSessionId: '{{ $selectedSession }}',
        currentBotId: {{ $bot->id }},
        isLive: {{ $isLive ? 'true' : 'false' }},
        currentSessions: [],

        init() {
            if (this.selectedSessionId) {
                this.startPolling();
            }
            this.scrollToBottom();
            this.loadSessionsList();
        },

        startPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
            this.pollingInterval = setInterval(() => {
                this.pollMessages();
                if (this.isLive) {
                    this.loadSessionsList();
                }
            }, 2000);
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },

        async loadSessionsList() {
            try {
                const url = `{{ route('bots.sessions-list', $bot) }}?date_preset=${encodeURIComponent(this.datePreset || '')}&filter_date=${encodeURIComponent(this.filterDate || '')}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data.sessions && data.sessions.length > 0) {
                    this.currentSessions = data.sessions;
                    this.renderSessionsList(data.sessions);
                } else {
                    this.renderEmptySessions();
                }
            } catch (error) {
                console.error('Load sessions list error:', error);
            }
        },

        renderSessionsList(sessions) {
            const container = document.getElementById('sessions-list');
            if (!container) return;

            let html = '';
            sessions.forEach(session => {
                const isActive = this.selectedSessionId === session.session_id;
                const lastTime = new Date(session.last_time);
                const formattedTime = lastTime.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                html += `
                    <a href="#"
                    data-session-id="${this.escapeHtml(session.session_id)}"
                    class="session-item block p-3 rounded-xl transition-all ${isActive ? 'gradient-warm text-amber-900 shadow-md' : 'hover:bg-amber-50 dark:hover:bg-gray-800'}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">${this.escapeHtml(session.icon)}</span>
                                    <span class="font-mono text-xs truncate ${isActive ? 'text-amber-900' : 'text-amber-600 dark:text-amber-400'}">
                                        ${this.escapeHtml(session.session_id.substring(0, 20))}...
                                    </span>
                                </div>
                                ${session.channel_name ? `
                                <p class="text-xs ${isActive ? 'text-amber-800/70' : 'text-amber-400'} mt-1">
                                    <i class="fas fa-link mr-1"></i>${this.escapeHtml(session.channel_name)}
                                </p>
                                ` : ''}
                                <div class="flex items-center gap-3 mt-2 text-xs ${isActive ? 'text-amber-800/70' : 'text-amber-400'}">
                                    <span>
                                        <i class="far fa-clock mr-1"></i>${formattedTime}
                                    </span>
                                    <span>
                                        <i class="fas fa-comment mr-1"></i>${session.msgs} msgs
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                `;
            });

            container.innerHTML = html;

            // Re-attach event listeners
            container.querySelectorAll('[data-session-id]').forEach(link => {
                const sessionId = link.dataset.sessionId;
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.selectSession(sessionId);
                });
            });
        },

        renderEmptySessions() {
            const container = document.getElementById('sessions-list');
            if (container) {
                container.innerHTML = `
                    <div class="p-8 text-center text-amber-400">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                        <p>No conversations yet</p>
                    </div>
                `;
            }
        },

        async pollMessages() {
            if (this.isPolling || !this.selectedSessionId) return;
            this.isPolling = true;

            try {
                const response = await fetch(`{{ route('bots.poll', $bot) }}?session_id=${encodeURIComponent(this.selectedSessionId)}&last_id=${this.lastMsgId}`);
                const data = await response.json();

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        const msgId = parseInt(msg.id, 10);
                        if (msgId > this.lastMsgId) {
                            this.appendMessage(msg);
                            this.lastMsgId = msgId;
                        }
                    });
                }

                if (data.session) {
                    this.updateSessionSidebar(data.session);
                }

            } catch (error) {
                console.error('Polling error:', error);
            } finally {
                this.isPolling = false;
            }
        },

        appendMessage(message) {
            const container = document.getElementById('chat-messages');
            if (!container) return;

            const emptyState = document.getElementById('empty-state');
            if (emptyState) emptyState.remove();

            const messageHtml = this.renderMessage(message);
            container.insertAdjacentHTML('beforeend', messageHtml);
            this.scrollToBottom();
        },

        updateSessionSidebar(session) {
            if (!session || session.session_id !== this.selectedSessionId) return;

            const sessionIndex = this.currentSessions.findIndex(s => s.session_id === session.session_id);
            if (sessionIndex !== -1) {
                this.currentSessions[sessionIndex] = {
                    ...this.currentSessions[sessionIndex],
                    msgs: session.msgs,
                    last_time: session.last_time
                };
            }

            this.renderSessionsList(this.currentSessions);
        },

        async sendReply() {
            const message = this.replyMessage.trim();
            if (!message) return;

            const btn = document.getElementById('reply-submit-btn');
            const input = document.getElementById('reply-message');

            if (btn) btn.disabled = true;
            if (input) input.disabled = true;

            try {
                const response = await fetch('{{ route('bots.send-reply', $bot) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        session_id: this.selectedSessionId,
                        message: message,
                        content: message
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                if (data.success && data.message) {
                    const msgId = parseInt(data.message.id, 10);
                    if (msgId > this.lastMsgId) {
                        this.appendMessage(data.message);
                        this.lastMsgId = msgId;
                    }

                    if (data.session_stats) {
                        this.updateSessionSidebar(data.session_stats);
                    }

                    await this.loadSessionsList();
                }

                this.replyMessage = '';

            } catch (error) {
                console.error('Send reply error:', error);
                Swal.fire('Oops!', 'Could not send message. Try again?', 'error');
            } finally {
                if (btn) btn.disabled = false;
                if (input) {
                    input.disabled = false;
                    input.focus();
                }
            }
        },

        async selectSession(sessionId) {
            if (sessionId === this.selectedSessionId) return;

            this.stopPolling();
            this.selectedSessionId = sessionId;
            this.lastMsgId = 0;

            const url = new URL(window.location.href);
            url.searchParams.set('session_id', sessionId);
            if (this.datePreset) url.searchParams.set('date_preset', this.datePreset);
            if (this.filterDate) url.searchParams.set('filter_date', this.filterDate);

            window.location.href = url.toString();
        },

        async clearSession(sessionId) {
            const result = await Swal.fire({
                title: 'Clear this chat?',
                text: 'This conversation will be removed.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, clear it',
                cancelButtonText: 'Cancel'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('{{ route('bots.clear-session', $bot) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ session_id: sessionId })
                });

                const data = await response.json();

                if (data.success) {
                    await this.loadSessionsList();

                    if (this.selectedSessionId === sessionId) {
                        this.stopPolling();
                        this.selectedSessionId = null;
                        const messagesContainer = document.getElementById('chat-messages');
                        if (messagesContainer) {
                            messagesContainer.innerHTML = `
                                <div class="flex-1 flex flex-col items-center justify-center h-full opacity-80">
                                    <div class="text-6xl mb-4">🗑️</div>
                                    <h3 class="text-xl font-semibold text-amber-700 dark:text-amber-300">Session Cleared</h3>
                                    <p class="text-amber-500 dark:text-amber-400 mt-2">Select another conversation from the sidebar</p>
                                </div>
                            `;
                        }
                    }

                    Swal.fire('Cleared!', 'The session has been cleared.', 'success');
                }
            } catch (error) {
                console.error('Clear session error:', error);
                Swal.fire('Error', 'Failed to clear session.', 'error');
            }
        },

        async clearAllChats() {
            const result = await Swal.fire({
                title: 'Tidy up all chats? 🧹',
                text: 'All conversations will be cleaned. This is permanent!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, clean up',
                cancelButtonText: 'Keep them'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('{{ route('bots.clear-all-chats', $bot) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    await this.loadSessionsList();

                    this.stopPolling();
                    this.selectedSessionId = null;
                    const messagesContainer = document.getElementById('chat-messages');
                    if (messagesContainer) {
                        messagesContainer.innerHTML = `
                            <div class="flex-1 flex flex-col items-center justify-center h-full opacity-80">
                                <div class="text-6xl mb-4">🧹</div>
                                <h3 class="text-xl font-semibold text-amber-700 dark:text-amber-300">All Chats Cleared</h3>
                                <p class="text-amber-500 dark:text-amber-400 mt-2">Ready for new conversations</p>
                            </div>
                        `;
                    }

                    Swal.fire('Cleaned!', 'All chats have been cleared.', 'success');
                }
            } catch (error) {
                console.error('Clear all chats error:', error);
                Swal.fire('Error', 'Failed to clear chats.', 'error');
            }
        },

        applyDateFilter() {
            const url = new URL(window.location.href);
            if (this.datePreset) {
                url.searchParams.set('date_preset', this.datePreset);
            } else {
                url.searchParams.delete('date_preset');
            }
            if (this.filterDate) {
                url.searchParams.set('filter_date', this.filterDate);
            } else {
                url.searchParams.delete('filter_date');
            }
            url.searchParams.delete('session_id');
            window.location.href = url.toString();
        },

        scrollToBottom() {
            setTimeout(() => {
                const container = document.getElementById('chat-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 100);
        },

        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        renderMessage(message) {
            const isGuest = message.role === 'guest';
            const isStaff = message.role === 'staff';

            const align = isStaff ? 'justify-end' : 'justify-start';
            const bgClass = isStaff ? 'gradient-warm text-amber-900' :
                           (isGuest ? 'bg-white dark:bg-gray-700 border border-amber-200 dark:border-gray-600 text-amber-800 dark:text-amber-200' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300');
            const roleLabel = isStaff ? 'You' : (isGuest ? 'Guest' : '🤖 AI Assistant');
            const roleIcon = isStaff ? 'fa-user-tie' : (isGuest ? 'fa-user' : 'fa-robot');
            const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            return `
                <div class="flex ${align} animate-gentle group">
                    <div class="max-w-[75%] ${bgClass} rounded-2xl p-3 shadow-sm">
                        <div class="flex items-center gap-2 text-xs opacity-70 mb-1">
                            <i class="fas ${roleIcon}"></i>
                            <span>${roleLabel}</span>
                            <span>•</span>
                            <span>${time}</span>
                        </div>
                        <div class="text-sm whitespace-pre-wrap break-words leading-relaxed">${parseMarkdown(message.content)}</div>
                    </div>
                </div>
            `;
        }
    }
}
</script>
@endpush
@endsection
