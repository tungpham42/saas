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

    <div x-data="chatManager()" x-init="init()" x-on:destroy="cleanup()" class="card-warm overflow-hidden">
        <div class="flex flex-col lg:flex-row h-[calc(100vh-200px)] min-h-[500px]">
            <!-- Sidebar with sessions -->
            <div class="w-full lg:w-80 border-r border-amber-100 dark:border-gray-700 flex flex-col bg-amber-50/30 dark:bg-gray-800/30">
                <div class="p-4 border-b border-amber-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-amber-800 dark:text-amber-200 flex items-center gap-2">
                            <i class="fas fa-comments text-amber-500"></i>
                            <span>{{ $isLive ? '💬 Live Chats' : '📜 Chat History' }}</span>
                        </h3>
                        @if($isLive)
                        <div class="flex items-center gap-2">
                            <span x-show="newSessionsCount > 0" x-text="newSessionsCount"
                                  class="bg-red-500 text-white text-xs rounded-full px-2 py-1 animate-pulse"></span>
                            <span x-show="autoRefreshEnabled" class="text-xs text-green-500">
                                <i class="fas fa-sync-alt fa-fw animate-spin"></i> Live
                            </span>
                        </div>
                        @endif
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

/* New session highlight animation */
@keyframes highlight-flash {
    0% {
        background-color: rgba(34, 197, 94, 0);
        border-left-color: transparent;
        transform: scale(1);
    }
    30% {
        background-color: rgba(34, 197, 94, 0.4);
        border-left-color: #22c55e;
        transform: scale(1.02);
    }
    70% {
        background-color: rgba(34, 197, 94, 0.2);
        border-left-color: #22c55e;
    }
    100% {
        background-color: rgba(34, 197, 94, 0);
        border-left-color: transparent;
        transform: scale(1);
    }
}

@keyframes message-pulse {
    0% { background-color: rgba(59, 130, 246, 0); }
    50% { background-color: rgba(59, 130, 246, 0.3); }
    100% { background-color: rgba(59, 130, 246, 0); }
}

.session-item-highlight {
    animation: highlight-flash 2s ease-out;
    border-left: 3px solid #22c55e;
}

.session-item-new-message {
    animation: message-pulse 1.5s ease-out;
}

/* Message counter badge */
.session-msg-count .new-badge {
    background-color: #ef4444;
    color: white;
    animation: pulse 1s infinite;
    font-size: 10px;
    padding: 2px 6px;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.9; }
}

/* Auto-refresh indicator */
@keyframes fadeInOut {
    0% { opacity: 0.3; }
    50% { opacity: 1; }
    100% { opacity: 0.3; }
}

.live-indicator {
    animation: fadeInOut 1.5s ease-in-out infinite;
}

/* Sidebar item hover effect */
.session-item {
    transition: all 0.2s ease;
}

.session-item:hover {
    transform: translateX(4px);
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
        sidebarInterval: null,
        isPolling: false,
        replyMessage: '',
        datePreset: '{{ $datePreset }}',
        filterDate: '{{ $filterDate }}',
        selectedSessionId: '{{ $selectedSession }}',
        currentBotId: {{ $bot->id }},
        isLive: {{ $isLive ? 'true' : 'false' }},
        currentSessions: [],
        knownSessionIds: new Set(),
        userManuallySelectedSession: {{ $selectedSession ? 'true' : 'false' }},
        newSessionsCount: 0,
        sessionMessageCounts: new Map(),
        autoRefreshEnabled: true,
        lastSidebarUpdate: Date.now(),
        isComponentDestroyed: false,

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
            // Initialize message counts for known sessions
            this.initSessionMessageCounts();

            // Pre-populate known sessions from existing HTML
            document.querySelectorAll('#sessions-list [data-session-id]').forEach(el => {
                const sessionId = el.getAttribute('data-session-id');
                this.knownSessionIds.add(sessionId);
                const countSpan = el.querySelector('.session-msg-count span:first-child');
                if (countSpan) {
                    this.sessionMessageCounts.set(sessionId, parseInt(countSpan.textContent) || 0);
                }
            });

            // Use event delegation for clicking sessions
            const sessionsListContainer = document.getElementById('sessions-list');
            if (sessionsListContainer) {
                sessionsListContainer.addEventListener('click', (e) => {
                    const item = e.target.closest('[data-session-id]');
                    if (item && !e.target.closest('a')) {
                        e.preventDefault();
                        this.userManuallySelectedSession = true;
                        this.newSessionsCount = 0;
                        this.selectSession(item.getAttribute('data-session-id'));
                    }
                });
            }

            if (this.selectedSessionId) {
                this.startPolling();
                this.checkForMoreMessages();
            }

            this.scrollToBottom();
            this.loadSessionsList();
            this.setupAutoScroll();
            this.setupInfiniteScroll();
            this.setupMessageScrollPagination();

            // Start sidebar auto-refresh every 2 seconds for live chat
            if (this.isLive) {
                this.startSidebarRefresh();
            }

            // Clean up on page unload
            window.addEventListener('beforeunload', () => {
                this.cleanup();
            });
        },

        cleanup() {
            this.isComponentDestroyed = true;
            this.stopPolling();
            this.stopSidebarRefresh();

            // Clear all intervals and timeouts
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
            if (this.sidebarInterval) {
                clearInterval(this.sidebarInterval);
                this.sidebarInterval = null;
            }
        },

        startSidebarRefresh() {
            if (this.sidebarInterval) {
                clearInterval(this.sidebarInterval);
            }

            // Refresh sidebar every 2 seconds
            this.sidebarInterval = setInterval(() => {
                if (this.autoRefreshEnabled && !this.loadingMoreSessions && !this.isComponentDestroyed) {
                    this.refreshSidebar();
                }
            }, 2000);
        },

        stopSidebarRefresh() {
            if (this.sidebarInterval) {
                clearInterval(this.sidebarInterval);
                this.sidebarInterval = null;
            }
        },

        async refreshSidebar() {
            if (this.isComponentDestroyed) return;

            try {
                const url = `{{ route('bots.sessions-list', $bot) }}?date_preset=${encodeURIComponent(this.datePreset || '')}&filter_date=${encodeURIComponent(this.filterDate || '')}&limit=100&t=${Date.now()}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data.sessions && data.sessions.length > 0) {
                    const oldSessionIds = new Set(this.currentSessions.map(s => s.session_id));
                    const newSessions = data.sessions.filter(s => !oldSessionIds.has(s.session_id));
                    const updatedSessions = data.sessions.filter(s => {
                        const oldSession = this.currentSessions.find(os => os.session_id === s.session_id);
                        return oldSession && (oldSession.msgs !== s.msgs || oldSession.last_time !== s.last_time);
                    });

                    // Update current sessions
                    this.currentSessions = data.sessions;

                    // Handle new sessions
                    if (newSessions.length > 0) {
                        newSessions.forEach(session => {
                            this.knownSessionIds.add(session.session_id);
                            this.sessionMessageCounts.set(session.session_id, session.msgs);
                            this.newSessionsCount++;

                            // Auto-jump if not manually selected
                            if (!this.userManuallySelectedSession) {
                                this.jumpToSession(session.session_id);
                            } else {
                                this.highlightSessionInSidebar(session.session_id, true);
                            }
                        });

                        this.showNotification(`${newSessions.length} new conversation${newSessions.length > 1 ? 's' : ''} started!`, 'info');
                    }

                    // Handle updated sessions (new messages)
                    if (updatedSessions.length > 0) {
                        updatedSessions.forEach(session => {
                            const oldCount = this.sessionMessageCounts.get(session.session_id) || 0;
                            if (session.msgs > oldCount) {
                                if (session.session_id !== this.selectedSessionId) {
                                    this.highlightSessionInSidebar(session.session_id, true);
                                    this.showNotification(`New message in ${session.channel_name || 'conversation'}`, 'message');
                                }
                                this.sessionMessageCounts.set(session.session_id, session.msgs);
                            }
                        });
                    }

                    // Re-render sidebar with updated data
                    if (!this.isComponentDestroyed) {
                        this.renderSessionsList(data.sessions);
                        this.lastSidebarUpdate = Date.now();
                    }
                }
            } catch (error) {
                console.error('Refresh sidebar error:', error);
            }
        },

        showNotification(message, type = 'info') {
            // Only show toast notification for important updates
            if (!this.userManuallySelectedSession || type === 'message') {
                const icon = type === 'info' ? '💬' : '📨';
                Swal.fire({
                    icon: type === 'info' ? 'info' : 'success',
                    title: icon + ' ' + message,
                    toast: true,
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    background: '#fef3c7',
                    iconColor: '#f59e0b'
                });
            }
        },

        initSessionMessageCounts() {
            document.querySelectorAll('#sessions-list [data-session-id]').forEach(el => {
                const sessionId = el.getAttribute('data-session-id');
                const countSpan = el.querySelector('.session-msg-count span:first-child');
                if (countSpan) {
                    this.sessionMessageCounts.set(sessionId, parseInt(countSpan.textContent) || 0);
                }
            });
        },

        highlightSessionInSidebar(sessionId, hasNewMessages = true) {
            const safeId = CSS.escape(sessionId);
            const sessionItem = document.querySelector(`[data-session-id="${safeId}"]`);
            if (sessionItem && hasNewMessages && !this.isComponentDestroyed) {
                // Add highlight class
                sessionItem.classList.add('session-item-highlight');
                sessionItem.classList.add('session-item-new-message');

                // Update message count badge to show new indicator
                const countSpan = sessionItem.querySelector('.session-msg-count');
                if (countSpan && !countSpan.querySelector('.new-badge')) {
                    const currentCount = this.sessionMessageCounts.get(sessionId) || 0;
                    const newCount = this.currentSessions.find(s => s.session_id === sessionId)?.msgs || currentCount;

                    if (newCount > currentCount) {
                        const badge = document.createElement('span');
                        badge.className = 'new-badge ml-1 px-1.5 py-0.5 rounded-full text-xs font-bold';
                        badge.textContent = `+${newCount - currentCount}`;
                        countSpan.appendChild(badge);
                    }
                }

                // Remove highlight after animation
                setTimeout(() => {
                    if (sessionItem && !this.isComponentDestroyed) {
                        sessionItem.classList.remove('session-item-highlight');
                        setTimeout(() => {
                            if (sessionItem && !this.isComponentDestroyed) {
                                sessionItem.classList.remove('session-item-new-message');
                            }
                        }, 1500);
                    }
                }, 2000);
            }
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
                    if (entry.isIntersecting && this.hasMoreSessions && !this.loadingMoreSessions && !this.isComponentDestroyed) {
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
            if (this.loadingMoreSessions || !this.hasMoreSessions || this.isComponentDestroyed) return;

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

                        container.insertBefore(sessionElement, sentinel);
                        this.sessionMessageCounts.set(session.session_id, session.msgs);
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
                if (messagesContainer.scrollTop === 0 && this.hasMoreMessages && !this.loadingOlderMessages && this.selectedSessionId && !this.isComponentDestroyed) {
                    this.loadOlderMessages();
                }
            });
        },

        async loadOlderMessages() {
            if (this.loadingOlderMessages || !this.hasMoreMessages || this.isComponentDestroyed) return;

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

                    [...data.messages].reverse().forEach(message => {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = message.html;
                        container.prepend(tempDiv.firstElementChild);
                    });

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
            if (this.currentSessions && this.currentSessions.length > 0 && !this.isComponentDestroyed) {
                this.currentSessions.forEach(session => {
                    if (!this.knownSessionIds.has(session.session_id)) {
                        this.knownSessionIds.add(session.session_id);
                        this.newSessionsCount++;
                    }
                });
            }
        },

        setupAutoScroll() {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList' && mutation.target.id === 'chat-messages' && !this.isComponentDestroyed) {
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
                if (!this.isComponentDestroyed) {
                    this.pollMessages();
                }
            }, 1500);
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },

        async loadSessionsList() {
            if (this.isComponentDestroyed) return;

            try {
                const url = `{{ route('bots.sessions-list', $bot) }}?date_preset=${encodeURIComponent(this.datePreset || '')}&filter_date=${encodeURIComponent(this.filterDate || '')}&limit=100`;
                const response = await fetch(url);
                const data = await response.json();

                if (data.sessions && data.sessions.length > 0) {
                    const newSessions = this.detectNewSessions(data.sessions);
                    this.currentSessions = data.sessions;
                    this.renderSessionsList(data.sessions);

                    if (this.isLive && newSessions.length > 0 && !this.userManuallySelectedSession && !this.isComponentDestroyed) {
                        const latestSession = data.sessions[0];
                        this.jumpToSession(latestSession.session_id);
                    } else if (this.isLive && newSessions.length > 0 && this.userManuallySelectedSession) {
                        newSessions.forEach(session => {
                            this.highlightSessionInSidebar(session.session_id, true);
                        });
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
            if (this.isComponentDestroyed) return;

            const container = document.getElementById('sessions-list');
            const sentinel = document.getElementById('sessions-sentinel');

            if (!container) return;

            const scrollTop = container.scrollTop;
            container.querySelectorAll('[data-session-id]').forEach(el => el.remove());

            sessions.forEach(session => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = session.html ?? this.buildSessionHTML(session);
                const el = tempDiv.firstElementChild;

                if (session.session_id === this.selectedSessionId) {
                    el.classList.add('bg-amber-100', 'dark:bg-gray-700', 'ring-2', 'ring-amber-400');
                }

                container.appendChild(el);
            });

            container.scrollTop = scrollTop;

            if (sentinel && !container.contains(sentinel)) {
                container.appendChild(sentinel);
            }
        },

        renderEmptySessions() {
            if (this.isComponentDestroyed) return;

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
            if (sessionId === this.selectedSessionId || this.isComponentDestroyed) return;

            this.showNewSessionNotification(sessionId);
            this.newSessionsCount = 0;
            this.selectSession(sessionId);

            setTimeout(() => {
                if (!this.isComponentDestroyed) {
                    this.userManuallySelectedSession = false;
                }
            }, 5000);
        },

        showNewSessionNotification(sessionId) {
            const session = this.currentSessions.find(s => s.session_id === sessionId);
            const channelName = session?.channel_name || 'New conversation';

            Swal.fire({
                icon: 'info',
                title: '💬 New Conversation!',
                text: `${channelName} - Auto-jumping to latest chat`,
                toast: true,
                timer: 4000,
                showConfirmButton: false,
                position: 'top-end',
                background: '#fef3c7',
                iconColor: '#f59e0b'
            });
        },

        async pollMessages() {
            if (this.isPolling || !this.selectedSessionId || this.isComponentDestroyed) return;
            this.isPolling = true;

            try {
                const response = await fetch(`{{ route('bots.poll', $bot) }}?session_id=${encodeURIComponent(this.selectedSessionId)}&last_id=${this.lastMsgId}`);
                const data = await response.json();

                if (data.messages && data.messages.length > 0 && !this.isComponentDestroyed) {
                    data.messages.forEach(msg => {
                        const msgId = parseInt(msg.id, 10);
                        if (msgId > this.lastMsgId) {
                            this.appendMessage(msg);
                            this.lastMsgId = msgId;
                        }
                    });
                }

                if (data.session && !this.isComponentDestroyed) {
                    this.updateSessionSidebar(data.session);

                    const currentCount = data.session.msgs;
                    const previousCount = this.sessionMessageCounts.get(data.session.session_id) || 0;
                    if (currentCount > previousCount && this.selectedSessionId !== data.session.session_id) {
                        this.highlightSessionInSidebar(data.session.session_id, true);
                    }
                    this.sessionMessageCounts.set(data.session.session_id, currentCount);
                }

            } catch (error) {
                console.error('Polling error:', error);
            } finally {
                this.isPolling = false;
            }
        },

        appendMessage(message) {
            if (this.isComponentDestroyed) return;

            const container = document.getElementById('chat-messages');
            if (!container) return;

            const emptyState = document.getElementById('empty-state');
            if (emptyState) emptyState.remove();

            const messageHtml = this.renderMessage(message);
            container.insertAdjacentHTML('beforeend', messageHtml);
            this.scrollToBottom();
        },

        updateSessionSidebar(session) {
            if (!session || session.session_id !== this.selectedSessionId || this.isComponentDestroyed) return;

            const safeId = CSS.escape(session.session_id);
            const sessionItem = document.querySelector(`[data-session-id="${safeId}"]`);
            if (sessionItem) {
                const container = document.getElementById('sessions-list');
                if (sessionItem && container) {
                    container.prepend(sessionItem);
                }

                const timeSpan = sessionItem.querySelector('.session-last-time span:first-child');
                if (timeSpan && session.last_time) {
                    timeSpan.textContent = new Date(session.last_time).toLocaleString();
                }

                const countSpan = sessionItem.querySelector('.session-msg-count span:first-child');
                if (countSpan && session.msgs) {
                    const oldCount = parseInt(countSpan.textContent) || 0;
                    countSpan.textContent = session.msgs;

                    if (session.msgs > oldCount && this.selectedSessionId !== session.session_id) {
                        this.highlightSessionInSidebar(session.session_id, true);
                    }
                }
            }
        },

        async sendReply() {
            const message = this.replyMessage.trim();
            if (!message || this.isComponentDestroyed) return;

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

                if (data.success && data.message && !this.isComponentDestroyed) {
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
            if (sessionId === this.selectedSessionId || this.isComponentDestroyed) return;

            this.userManuallySelectedSession = true;

            const safeId = CSS.escape(sessionId);
            const sessionItem = document.querySelector(`[data-session-id="${safeId}"]`);
            if (sessionItem) {
                sessionItem.classList.remove('session-item-highlight', 'session-item-new-message');
                const badge = sessionItem.querySelector('.new-badge');
                if (badge) badge.remove();
            }

            const currentCount = this.sessionMessageCounts.get(sessionId) || 0;
            this.sessionMessageCounts.set(sessionId, currentCount);

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

            if (!result.isConfirmed || this.isComponentDestroyed) return;

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

                if (data.success && !this.isComponentDestroyed) {
                    this.knownSessionIds.delete(sessionId);
                    this.sessionMessageCounts.delete(sessionId);

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

            if (!result.isConfirmed || this.isComponentDestroyed) return;

            try {
                const response = await fetch('{{ route('bots.clear-all-chats', $bot) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success && !this.isComponentDestroyed) {
                    this.knownSessionIds.clear();
                    this.sessionMessageCounts.clear();
                    this.newSessionsCount = 0;

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
            if (this.isComponentDestroyed) return;

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
                if (container && !this.isComponentDestroyed) {
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
        },

        buildSessionHTML(session) {
            const channelIcon = session.icon || '💬';
            const channelName = session.channel_name || (session.channel_type === 'web' ? 'Website' : 'Chat');
            const lastTime = session.last_time ? new Date(session.last_time).toLocaleString() : '';

            return `
                <div class="session-item group p-3 rounded-xl cursor-pointer transition-all hover:bg-amber-50 dark:hover:bg-gray-700/50 border border-transparent hover:border-amber-200 dark:hover:border-gray-600" data-session-id="${session.session_id}">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white shadow-sm">
                            <span class="text-lg">${channelIcon}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-amber-800 dark:text-amber-200 truncate">${channelName}</p>
                                <span class="session-msg-count text-xs text-amber-500 dark:text-amber-400"><span>${session.msgs}</span> msgs</span>
                            </div>
                            <div class="session-last-time flex items-center gap-2 text-xs text-amber-400 dark:text-amber-500 mt-0.5">
                                <i class="fas fa-clock text-xs"></i>
                                <span>${lastTime || 'Just now'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }
}
</script>
@endpush
@endsection
