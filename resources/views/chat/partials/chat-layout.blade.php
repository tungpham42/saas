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
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-amber-800 dark:text-amber-200 flex items-center gap-2">
                            <i class="fas fa-comments text-amber-500"></i>
                            <span>{{ $isLive ? '💬 Live Chats' : '📜 Chat History' }}</span>
                        </h3>
                    </div>

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
                        @php
                            $isActive = $selectedSession === $session->session_id;
                            $sessionInfo = parseSessionId($session->session_id, $bot);
                        @endphp
                        @include('chat.partials.session-item', [
                            'session' => $session,
                            'sessionInfo' => $sessionInfo,
                            'isActive' => $isActive,
                            'bot' => $bot,
                            'isLive' => $isLive
                        ])
                    @empty
                    <div class="p-8 text-center text-amber-400">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                        <p>No conversations yet</p>
                    </div>
                    @endforelse

                    <!-- Loading indicator for infinite scroll -->
                    <div id="sessions-loading" class="text-center p-4 hidden">
                        <div class="loading-spinner mx-auto"></div>
                        <p class="text-xs text-amber-500 mt-2">Loading more...</p>
                    </div>
                </div>
            </div>

            <!-- Chat messages area -->
            <div class="flex-1 flex flex-col bg-white dark:bg-gray-800">
                @if($selectedSession)
                    @php
                        $sessionInfo = parseSessionId($selectedSession, $bot);
                    @endphp
                    <div class="p-4 border-b border-amber-100 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-wrap justify-between items-center gap-3 sticky top-0 z-10">
                        <div class="flex items-center gap-2">
                            <div class="gradient-warm rounded-full w-8 h-8 flex items-center justify-center">
                                <i class="fas fa-comment-dots text-amber-900 text-xs"></i>
                            </div>
                            <div>
                                <span class="font-mono text-sm text-amber-700 dark:text-amber-300">{{ substr($selectedSession, 0, 25) }}...</span>
                                @if(isset($sessionInfo['channel_name']))
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
                        <div id="messages-loading" class="text-center p-4 hidden">
                            <div class="loading-spinner mx-auto"></div>
                            <p class="text-xs text-amber-500 mt-2">Loading older messages...</p>
                        </div>

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
<style>
.loading-spinner {
    display: inline-block;
    width: 24px;
    height: 24px;
    border: 2px solid rgba(245, 158, 11, 0.3);
    border-radius: 50%;
    border-top-color: #f59e0b;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
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
        knownSessionIds: new Set(),
        userManuallySelectedSession: false, // Track if user manually selected a session

        // Pagination properties
        sessionsPage: 1,
        sessionsPerPage: {{ $isLive ? 20 : 50 }},
        sessionsTotal: {{ $sessions->total() ?? 0 }},
        sessionsLastPage: {{ $sessions->lastPage() ?? 1 }},
        loadingMoreSessions: false,
        hasMoreSessions: {{ ($sessions->currentPage() ?? 1) < ($sessions->lastPage() ?? 1) ? 'true' : 'false' }},

        messagesPage: 1,
        messagesPerPage: 50,
        hasMoreMessages: false,
        loadingOlderMessages: false,

        init() {
            if (this.selectedSessionId) {
                this.startPolling();
                this.checkForMoreMessages();
            }
            this.scrollToBottom();
            this.loadSessionsList();
            this.updateKnownSessions();
            this.setupAutoScroll();
            this.setupInfiniteScroll();
            this.setupMessageScrollPagination();
        },

        checkForMoreMessages() {
            const totalMessages = {{ $messages ? $messages->count() : 0 }};
            this.hasMoreMessages = totalMessages >= this.messagesPerPage;
            this.messagesPage = 1;
        },

        setupInfiniteScroll() {
            const sessionsList = document.getElementById('sessions-list');
            if (!sessionsList) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && this.hasMoreSessions && !this.loadingMoreSessions) {
                        this.loadMoreSessions();
                    }
                });
            }, { threshold: 0.1 });

            const sentinel = document.createElement('div');
            sentinel.id = 'sessions-sentinel';
            sentinel.className = 'h-1';
            sessionsList.appendChild(sentinel);
            observer.observe(sentinel);

            this.sessionsSentinel = sentinel;
            this.sessionsObserver = observer;
        },

        async loadMoreSessions() {
            if (this.loadingMoreSessions || !this.hasMoreSessions) return;

            this.loadingMoreSessions = true;
            document.getElementById('sessions-loading')?.classList.remove('hidden');
            const nextPage = this.sessionsPage + 1;

            try {
                const url = `{{ route('bots.load-more-sessions', $bot) }}?date_preset=${encodeURIComponent(this.datePreset || '')}&filter_date=${encodeURIComponent(this.filterDate || '')}&page=${nextPage}&per_page=${this.sessionsPerPage}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data.sessions && data.sessions.length > 0) {
                    const container = document.getElementById('sessions-list');
                    const sentinel = document.getElementById('sessions-sentinel');

                    data.sessions.forEach(session => {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = session.html;
                        const sessionElement = tempDiv.firstElementChild;

                        sessionElement.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.userManuallySelectedSession = true; // Mark as manual selection
                            this.selectSession(session.session_id);
                        });

                        container.insertBefore(sessionElement, sentinel);
                    });

                    this.sessionsPage = data.current_page;
                    this.hasMoreSessions = data.has_more;

                    this.currentSessions = [...this.currentSessions, ...data.sessions];
                    this.updateKnownSessions();
                }
            } catch (error) {
                console.error('Load more sessions error:', error);
            } finally {
                this.loadingMoreSessions = false;
                document.getElementById('sessions-loading')?.classList.add('hidden');
            }
        },

        setupMessageScrollPagination() {
            const messagesContainer = document.getElementById('chat-messages');
            if (!messagesContainer) return;

            messagesContainer.addEventListener('scroll', () => {
                if (messagesContainer.scrollTop === 0 && this.hasMoreMessages && !this.loadingOlderMessages && this.selectedSessionId) {
                    this.loadOlderMessages();
                }
            });
        },

        async loadOlderMessages() {
            if (this.loadingOlderMessages || !this.hasMoreMessages) return;

            this.loadingOlderMessages = true;
            const loadingEl = document.getElementById('messages-loading');
            if (loadingEl) loadingEl.classList.remove('hidden');

            const nextPage = this.messagesPage + 1;

            try {
                const url = `{{ route('bots.load-more-messages', $bot) }}?session_id=${encodeURIComponent(this.selectedSessionId)}&page=${nextPage}&per_page=${this.messagesPerPage}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data && data.messages && data.messages.length > 0) {
                    const container = document.getElementById('chat-messages');
                    const scrollHeight = container.scrollHeight;
                    const scrollTop = container.scrollTop;

                    // Prepend older messages (reverse to maintain order)
                    [...data.messages].reverse().forEach(message => {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = message.html;
                        container.prepend(tempDiv.firstElementChild);
                    });

                    // Adjust scroll position
                    const newScrollHeight = container.scrollHeight;
                    container.scrollTop = scrollTop + (newScrollHeight - scrollHeight);

                    this.messagesPage = data.current_page;
                    this.hasMoreMessages = data.has_more;
                } else {
                    this.hasMoreMessages = false;
                }
            } catch (error) {
                console.error('Load older messages error:', error);
            } finally {
                this.loadingOlderMessages = false;
                const loadingEl = document.getElementById('messages-loading');
                if (loadingEl) loadingEl.classList.add('hidden');
            }
        },

        updateKnownSessions() {
            if (this.currentSessions && this.currentSessions.length > 0) {
                this.currentSessions.forEach(session => {
                    this.knownSessionIds.add(session.session_id);
                });
            }
        },

        setupAutoScroll() {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList' && mutation.target.id === 'chat-messages') {
                        this.scrollToBottom();
                    }
                });
            });

            const messagesContainer = document.getElementById('chat-messages');
            if (messagesContainer) {
                observer.observe(messagesContainer, { childList: true, subtree: true });
            }
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
                    const newSessions = this.detectNewSessions(data.sessions);
                    this.currentSessions = data.sessions;
                    this.renderSessionsList(data.sessions);

                    // Auto-jump to new session if in live mode
                    if (this.isLive && newSessions.length > 0) {
                        const latestSession = data.sessions[0];

                        // Only auto-jump if the user hasn't manually selected a different session
                        if (!this.userManuallySelectedSession) {
                            this.jumpToSession(latestSession.session_id);
                        }
                    }

                    this.updateKnownSessions();
                } else {
                    this.renderEmptySessions();
                }
            } catch (error) {
                console.error('Load sessions list error:', error);
            }
        },

        detectNewSessions(newSessions) {
            const newOnes = [];
            newSessions.forEach(session => {
                if (!this.knownSessionIds.has(session.session_id)) {
                    newOnes.push(session);
                }
            });
            return newOnes;
        },

        renderSessionsList(sessions) {
            const container = document.getElementById('sessions-list');
            if (!container) return;

            // Get existing session items and add click handlers to new ones
            const existingItems = container.querySelectorAll('.session-item');

            if (existingItems.length > 0 && sessions.length > 0) {
                // Update only the existing items' data (like msg count, last time)
                sessions.slice(0, existingItems.length).forEach((session, index) => {
                    const item = existingItems[index];
                    if (item) {
                        const timeSpan = item.querySelector('.session-last-time span:first-child');
                        const countSpan = item.querySelector('.session-msg-count span:first-child');
                        if (timeSpan) timeSpan.textContent = new Date(session.last_time).toLocaleString();
                        if (countSpan) countSpan.textContent = session.msgs;

                        // Ensure click handler is attached
                        item.removeEventListener('click', this.handleSessionClick);
                        item.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.userManuallySelectedSession = true;
                            this.selectSession(session.session_id);
                        });
                    }
                });
            }
        },

        renderEmptySessions() {
            const container = document.getElementById('sessions-list');
            if (container && !container.querySelector('.session-item')) {
                container.innerHTML = `
                    <div class="p-8 text-center text-amber-400">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                        <p>No conversations yet</p>
                    </div>
                `;
            }
        },

        async jumpToSession(sessionId) {
            if (sessionId === this.selectedSessionId) return;
            this.showNewSessionNotification(sessionId);
            this.selectSession(sessionId);

            // Reset manual selection flag after 5 seconds to allow future auto-jumps
            setTimeout(() => {
                this.userManuallySelectedSession = false;
            }, 5000);
        },

        showNewSessionNotification(sessionId) {
            const message = this.userManuallySelectedSession ?
                'New chat available (staying on current view)' :
                'Auto-jumping to the latest chat';

            Swal.fire({
                icon: 'info',
                title: 'New Conversation Started! 💬',
                text: message,
                toast: true,
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                background: '#fef3c7',
                iconColor: '#f59e0b'
            });
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

            const sessionItem = document.querySelector(`[data-session-id="${session.session_id}"]`);
            if (sessionItem) {
                const timeSpan = sessionItem.querySelector('.session-last-time span:first-child');
                const countSpan = sessionItem.querySelector('.session-msg-count span:first-child');
                if (timeSpan) timeSpan.textContent = new Date(session.last_time).toLocaleString();
                if (countSpan) countSpan.textContent = session.msgs;
            }
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

            // Mark that user has manually selected a session
            this.userManuallySelectedSession = true;

            this.stopPolling();
            this.selectedSessionId = sessionId;
            this.lastMsgId = 0;
            this.messagesPage = 1;
            this.hasMoreMessages = false;

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
            const isUser = message.role === 'user';
            const isAdmin = message.role === 'admin';

            const align = isAdmin ? 'justify-end' : 'justify-start';
            const bgClass = isAdmin ? 'gradient-warm text-amber-900' :
                           (isUser ? 'bg-white dark:bg-gray-700 border border-amber-200 dark:border-gray-600 text-amber-800 dark:text-amber-200' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300');
            const roleLabel = isAdmin ? 'You' : (isUser ? 'Customer' : '🤖 AI Assistant');
            const roleIcon = isAdmin ? 'fa-user-tie' : (isUser ? 'fa-user' : 'fa-robot');
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
