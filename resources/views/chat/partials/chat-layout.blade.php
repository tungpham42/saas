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
                            <span class="text-xs text-green-500">
                                <i class="fas fa-circle text-[8px] animate-pulse"></i> Live
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
                    <template x-for="session in displayedSessions" :key="session.session_id">
                        <div
                            :data-session-id="session.session_id"
                            :class="{
                                'bg-amber-100 dark:bg-gray-700 ring-2 ring-amber-400': session.session_id === selectedSessionId,
                                'session-item-highlight': session.highlighted,
                                'session-item-new-message': session.newMessage
                            }"
                            @click="selectSession(session.session_id)"
                            class="session-item group p-3 rounded-xl cursor-pointer transition-all hover:bg-amber-50 dark:hover:bg-gray-700/50 border border-transparent hover:border-amber-200 dark:hover:border-gray-600"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white shadow-sm">
                                    <span class="text-lg" x-text="session.icon || '💬'"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-amber-800 dark:text-amber-200 truncate" x-text="session.channel_name || (session.channel_type === 'web' ? 'Website' : 'Chat')"></p>
                                        <span class="session-msg-count text-xs text-amber-500 dark:text-amber-400">
                                            <span x-text="session.msgs"></span> msgs
                                            <span x-show="session.newMessagesCount > 0"
                                                  x-text="'+' + session.newMessagesCount"
                                                  class="new-badge ml-1 px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white animate-pulse"></span>
                                        </span>
                                    </div>
                                    <div class="session-last-time flex items-center gap-2 text-xs text-amber-400 dark:text-amber-500 mt-0.5">
                                        <i class="fas fa-clock text-xs"></i>
                                        <span x-text="formatTime(session.last_time)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="displayedSessions.length === 0 && !loadingMoreSessions" class="p-8 text-center text-amber-400">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                        <p>No conversations yet</p>
                    </div>

                    <div x-show="loadingMoreSessions" class="text-center p-4">
                        <div class="loading-spinner mx-auto"></div>
                        <p class="text-xs text-amber-500 mt-2">Loading more...</p>
                    </div>

                    <div id="sessions-sentinel" class="h-1"></div>
                </div>
            </div>

            <!-- Chat messages area -->
            <div class="flex-1 flex flex-col bg-white dark:bg-gray-800">
                <template x-if="selectedSessionId">
                    <div class="flex flex-col h-full">
                        <div class="p-4 border-b border-amber-100 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-wrap justify-between items-center gap-3 sticky top-0 z-10">
                            <div class="flex items-center gap-2">
                                <div class="gradient-warm rounded-full w-8 h-8 flex items-center justify-center">
                                    <i class="fas fa-comment-dots text-amber-900 text-xs"></i>
                                </div>
                                <div>
                                    <span class="font-mono text-sm text-amber-700 dark:text-amber-300" x-text="selectedSessionId.substring(0, 25) + '...'"></span>
                                    <span x-show="selectedSessionInfo?.channel_name"
                                          x-text="selectedSessionInfo.channel_name"
                                          class="ml-2 px-2 py-0.5 bg-amber-50 dark:bg-gray-700 rounded-full text-xs text-amber-600"></span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a :href="`{{ route('bots.export-session', $bot) }}?session_id=${encodeURIComponent(selectedSessionId)}`"
                                   class="px-3 py-1.5 text-sm bg-amber-50 dark:bg-gray-700 hover:bg-amber-100 dark:hover:bg-gray-600 rounded-xl transition flex items-center gap-1 text-amber-600">
                                    <i class="fas fa-download"></i>
                                    <span>Save</span>
                                </a>
                                @if(!$isLive)
                                <button @click="clearSession(selectedSessionId)"
                                        class="px-3 py-1.5 text-sm bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-xl transition flex items-center gap-1">
                                    <i class="fas fa-trash-alt"></i>
                                    <span>Clear</span>
                                </button>
                                @endif
                            </div>
                        </div>

                        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-amber-50/30 to-white dark:from-gray-900 dark:to-gray-800">
                            <div x-show="loadingOlderMessages" class="text-center p-4">
                                <div class="loading-spinner mx-auto"></div>
                                <p class="text-xs text-amber-500 mt-2">Loading older messages...</p>
                            </div>

                            <template x-for="msg in messages" :key="msg.id">
                                <div :class="msg.role === 'admin' ? 'flex justify-end' : 'flex justify-start'" class="animate-gentle group">
                                    <div :class="msg.role === 'admin' ? 'gradient-warm text-amber-900' : (msg.role === 'user' ? 'bg-white dark:bg-gray-700 border border-amber-200 dark:border-gray-600 text-amber-800 dark:text-amber-200' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300')" class="max-w-[75%] rounded-2xl p-3 shadow-sm">
                                        <div class="flex items-center gap-2 text-xs opacity-70 mb-1">
                                            <i :class="msg.role === 'admin' ? 'fas fa-user-tie' : (msg.role === 'user' ? 'fas fa-user' : 'fas fa-robot')"></i>
                                            <span x-text="msg.role === 'admin' ? 'You' : (msg.role === 'user' ? 'Customer' : 'AI Assistant')"></span>
                                            <span>•</span>
                                            <span x-text="formatTime(msg.created_at, true)"></span>
                                        </div>
                                        <div class="text-sm whitespace-pre-wrap break-words leading-relaxed" x-html="parseMarkdown(msg.content)"></div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="messages.length === 0 && !loadingOlderMessages" class="flex-1 flex flex-col items-center justify-center h-full opacity-80">
                                <div class="text-6xl mb-4">💭</div>
                                <h3 class="text-xl font-semibold text-amber-700 dark:text-amber-300">Start the Conversation</h3>
                                <p class="text-amber-500 dark:text-amber-400 mt-2">Be the first to say hello!</p>
                            </div>
                        </div>

                        @if($isLive)
                        <div class="p-4 border-t border-amber-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <form @submit.prevent="sendReply" class="flex gap-2">
                                @csrf
                                <div class="flex-1 relative">
                                    <input type="text" x-model="replyMessage"
                                        class="w-full px-4 py-3 pr-12 border border-amber-200 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-amber-500 focus:border-transparent bg-white dark:bg-gray-700 text-amber-800 dark:text-amber-200"
                                        placeholder="Type a warm reply..." autocomplete="off">
                                    <button type="submit" :disabled="!replyMessage.trim()"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-1.5 btn-soft rounded-xl text-sm disabled:opacity-50">
                                        Send 💝
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </template>

                <div x-show="!selectedSessionId" class="flex-1 flex items-center justify-center">
                    <div class="text-center p-8">
                        <div class="text-6xl mb-4">🤗</div>
                        <h3 class="text-xl font-semibold text-amber-700 dark:text-amber-300">Welcome to the Chat</h3>
                        <p class="text-amber-500 dark:text-amber-400 mt-2">Choose a conversation from the sidebar to get started</p>
                    </div>
                </div>
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
.new-badge {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.9; }
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
        // State
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

        // Session data
        sessions: [],
        displayedSessions: [],
        sessionMessageCounts: new Map(),
        newSessionsCount: 0,
        userManuallySelectedSession: {{ $selectedSession ? 'true' : 'false' }},

        // UI state
        autoRefreshEnabled: true,
        isComponentDestroyed: false,
        loadingMoreSessions: false,
        hasMoreSessions: true,
        sessionsPage: 1,
        sessionsPerPage: {{ $isLive ? 20 : 50 }},

        // Messages
        messages: [],
        messagesPage: 1,
        messagesPerPage: 50,
        hasMoreMessages: false,
        loadingOlderMessages: false,

        // Computed
        get selectedSessionInfo() {
            return this.sessions.find(s => s.session_id === this.selectedSessionId);
        },

        init() {
            // Initialize sessions from server data
            const initialSessions = {!! json_encode($sessions->map(function($session) use ($bot) {
                $sessionInfo = parseSessionId($session->session_id, $bot);
                return [
                    'session_id' => $session->session_id,
                    'last_time' => $session->last_time?->toISOString(),
                    'msgs' => $session->msgs,
                    'channel_name' => $sessionInfo['channel_name'] ?? null,
                    'icon' => $sessionInfo['icon'] ?? '💬',
                    'channel_type' => $sessionInfo['type'] ?? 'web',
                    'highlighted' => false,
                    'newMessage' => false,
                    'newMessagesCount' => 0,
                ];
            })->toArray()) !!};

            this.sessions = initialSessions;
            this.displayedSessions = [...this.sessions];

            // Initialize known session IDs and message counts
            this.sessions.forEach(session => {
                this.sessionMessageCounts.set(session.session_id, session.msgs);
            });

            // Initialize messages
            const initialMessages = {!! json_encode($messages ? $messages->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'session_id' => $msg->session_id,
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'created_at' => $msg->created_at?->toISOString(),
                ];
            })->toArray() : []) !!};

            this.messages = initialMessages;

            // Setup event listeners
            if (this.selectedSessionId) {
                this.startPolling();
                this.checkForMoreMessages();
            }

            this.scrollToBottom();
            this.setupAutoScroll();
            this.setupInfiniteScroll();
            this.setupMessageScrollPagination();

            // Start auto-refresh for live chat
            if (this.isLive) {
                this.startSidebarRefresh();
            }

            // Clean up on page unload
            window.addEventListener('beforeunload', () => this.cleanup());
        },

        cleanup() {
            this.isComponentDestroyed = true;
            this.stopPolling();
            this.stopSidebarRefresh();
        },

        startSidebarRefresh() {
            if (this.sidebarInterval) clearInterval(this.sidebarInterval);

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
                    this.updateSessionsList(data.sessions);
                }
            } catch (error) {
                console.error('Refresh sidebar error:', error);
            }
        },

        updateSessionsList(newSessionsData) {
            // Create a map of existing sessions
            const existingSessionsMap = new Map();
            this.sessions.forEach(s => existingSessionsMap.set(s.session_id, s));

            // Track new and updated sessions
            let hasNewSessions = false;
            let hasUpdates = false;

            // Process each new session
            newSessionsData.forEach(newData => {
                const existing = existingSessionsMap.get(newData.session_id);
                const newMsgCount = newData.msgs;
                const oldMsgCount = existing?.msgs || 0;

                if (!existing) {
                    // Brand new session
                    hasNewSessions = true;
                    this.newSessionsCount++;

                    this.sessions.unshift({
                        session_id: newData.session_id,
                        last_time: newData.last_time,
                        msgs: newMsgCount,
                        channel_name: newData.channel_name,
                        icon: newData.icon,
                        channel_type: newData.channel_type,
                        highlighted: true,
                        newMessage: true,
                        newMessagesCount: newMsgCount,
                    });

                    this.sessionMessageCounts.set(newData.session_id, newMsgCount);

                    // Auto-jump if not manually selected
                    if (!this.userManuallySelectedSession) {
                        this.jumpToSession(newData.session_id);
                    }
                } else {
                    // Existing session - check for updates
                    if (newMsgCount > oldMsgCount) {
                        hasUpdates = true;
                        const newMessages = newMsgCount - oldMsgCount;

                        // Update the session in the array
                        const index = this.sessions.findIndex(s => s.session_id === newData.session_id);
                        if (index !== -1) {
                            this.sessions[index] = {
                                ...this.sessions[index],
                                last_time: newData.last_time,
                                msgs: newMsgCount,
                                newMessagesCount: newMessages,
                                highlighted: true,
                                newMessage: true,
                            };

                            // Move to top if it's not the current session
                            if (newData.session_id !== this.selectedSessionId && index > 0) {
                                const [moved] = this.sessions.splice(index, 1);
                                this.sessions.unshift(moved);
                            }

                            // Highlight if not current session
                            if (newData.session_id !== this.selectedSessionId) {
                                this.highlightSessionInSidebar(newData.session_id, newMessages);
                            }

                            this.sessionMessageCounts.set(newData.session_id, newMsgCount);
                        }
                    }
                }
            });

            // Re-sort sessions (newest first)
            this.sessions.sort((a, b) => new Date(b.last_time) - new Date(a.last_time));

            // Update displayed sessions
            this.displayedSessions = [...this.sessions];

            // Show notifications
            if (hasNewSessions && !this.userManuallySelectedSession) {
                this.showNotification('New conversation started!', 'info');
            } else if (hasUpdates && !this.userManuallySelectedSession) {
                this.showNotification('New message received!', 'message');
            }

            // Clear highlight flags after animation
            setTimeout(() => {
                this.sessions.forEach(session => {
                    session.highlighted = false;
                    session.newMessage = false;
                    session.newMessagesCount = 0;
                });
                this.displayedSessions = [...this.sessions];
            }, 2000);
        },

        highlightSessionInSidebar(sessionId, newMessagesCount) {
            const index = this.displayedSessions.findIndex(s => s.session_id === sessionId);
            if (index !== -1) {
                this.displayedSessions[index].highlighted = true;
                this.displayedSessions[index].newMessagesCount = newMessagesCount;
                // Reset after animation
                setTimeout(() => {
                    if (this.displayedSessions[index]) {
                        this.displayedSessions[index].highlighted = false;
                        this.displayedSessions[index].newMessagesCount = 0;
                    }
                }, 2000);
            }
        },

        showNotification(message, type = 'info') {
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
        },

        setupInfiniteScroll() {
            const sentinel = document.getElementById('sessions-sentinel');
            if (!sentinel) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && this.hasMoreSessions && !this.loadingMoreSessions && !this.isComponentDestroyed) {
                        this.loadMoreSessions();
                    }
                });
            }, { threshold: 0.1 });

            observer.observe(sentinel);
            this.sessionsObserver = observer;
        },

        async loadMoreSessions() {
            if (this.loadingMoreSessions || !this.hasMoreSessions || this.isComponentDestroyed) return;

            this.loadingMoreSessions = true;
            const nextPage = this.sessionsPage + 1;

            try {
                const url = `{{ route('bots.load-more-sessions', $bot) }}?date_preset=${encodeURIComponent(this.datePreset || '')}&filter_date=${encodeURIComponent(this.filterDate || '')}&page=${nextPage}&per_page=${this.sessionsPerPage}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data.sessions && data.sessions.length > 0) {
                    data.sessions.forEach(sessionData => {
                        if (!this.sessions.find(s => s.session_id === sessionData.session_id)) {
                            this.sessions.push({
                                session_id: sessionData.session_id,
                                last_time: sessionData.last_time,
                                msgs: sessionData.msgs,
                                channel_name: sessionData.channel_name,
                                icon: sessionData.icon,
                                channel_type: sessionData.channel_type,
                                highlighted: false,
                                newMessage: false,
                                newMessagesCount: 0,
                            });
                        }
                    });

                    this.sessions.sort((a, b) => new Date(b.last_time) - new Date(a.last_time));
                    this.displayedSessions = [...this.sessions];

                    this.sessionsPage = data.current_page;
                    this.hasMoreSessions = data.has_more;
                }
            } catch (error) {
                console.error('Load more sessions error:', error);
            } finally {
                this.loadingMoreSessions = false;
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
            const nextPage = this.messagesPage + 1;

            try {
                const url = `{{ route('bots.load-more-messages', $bot) }}?session_id=${encodeURIComponent(this.selectedSessionId)}&page=${nextPage}&per_page=${this.messagesPerPage}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data && data.messages && data.messages.length > 0) {
                    const container = document.getElementById('chat-messages');
                    const scrollHeight = container.scrollHeight;
                    const scrollTop = container.scrollTop;

                    const oldMessages = data.messages.map(msg => ({
                        id: msg.id,
                        session_id: msg.session_id,
                        role: msg.role,
                        content: msg.content,
                        created_at: msg.created_at,
                    }));

                    this.messages = [...oldMessages.reverse(), ...this.messages];

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
            }
        },

        setupAutoScroll() {
            const observer = new MutationObserver(() => {
                this.scrollToBottom();
            });

            const messagesContainer = document.getElementById('chat-messages');
            if (messagesContainer) {
                observer.observe(messagesContainer, { childList: true, subtree: true });
            }
        },

        startPolling() {
            if (this.pollingInterval) clearInterval(this.pollingInterval);

            this.pollingInterval = setInterval(() => {
                if (!this.isComponentDestroyed) this.pollMessages();
            }, 1500);
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },

        async pollMessages() {
            if (this.isPolling || !this.selectedSessionId || this.isComponentDestroyed) return;
            this.isPolling = true;

            try {
                const response = await fetch(`{{ route('bots.poll', $bot) }}?session_id=${encodeURIComponent(this.selectedSessionId)}&last_id=${this.lastMsgId}`);
                const data = await response.json();

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        const msgId = parseInt(msg.id, 10);
                        if (msgId > this.lastMsgId) {
                            this.messages.push({
                                id: msg.id,
                                session_id: msg.session_id,
                                role: msg.role,
                                content: msg.content,
                                created_at: msg.created_at,
                            });
                            this.lastMsgId = msgId;
                        }
                    });
                    this.scrollToBottom();
                }

                if (data.session && !this.isComponentDestroyed) {
                    // Update session in list
                    const sessionIndex = this.sessions.findIndex(s => s.session_id === data.session.session_id);
                    if (sessionIndex !== -1) {
                        this.sessions[sessionIndex].last_time = data.session.last_time;
                        this.sessions[sessionIndex].msgs = data.session.msgs;

                        // Move to top if not current session
                        if (data.session.session_id !== this.selectedSessionId && sessionIndex > 0) {
                            const [moved] = this.sessions.splice(sessionIndex, 1);
                            this.sessions.unshift(moved);
                            this.highlightSessionInSidebar(data.session.session_id, 1);
                        }

                        this.displayedSessions = [...this.sessions];
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            } finally {
                this.isPolling = false;
            }
        },

        async sendReply() {
            const message = this.replyMessage.trim();
            if (!message || this.isComponentDestroyed) return;

            const btn = document.querySelector('#reply-form button[type="submit"]');
            const input = document.querySelector('#reply-form input');

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

                const data = await response.json();

                if (data.success && data.message && !this.isComponentDestroyed) {
                    const msgId = parseInt(data.message.id, 10);
                    if (msgId > this.lastMsgId) {
                        this.messages.push({
                            id: data.message.id,
                            session_id: data.message.session_id,
                            role: data.message.role,
                            content: data.message.content,
                            created_at: data.message.created_at,
                        });
                        this.lastMsgId = msgId;
                        this.scrollToBottom();
                    }

                    // Update session stats in sidebar
                    if (data.session_stats) {
                        const sessionIndex = this.sessions.findIndex(s => s.session_id === data.session_stats.session_id);
                        if (sessionIndex !== -1) {
                            this.sessions[sessionIndex].msgs = data.session_stats.msgs;
                            this.sessions[sessionIndex].last_time = data.session_stats.last_time;
                            this.displayedSessions = [...this.sessions];
                        }
                    }
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
            this.newSessionsCount = 0;

            // Clear highlight from this session
            const sessionIndex = this.sessions.findIndex(s => s.session_id === sessionId);
            if (sessionIndex !== -1) {
                this.sessions[sessionIndex].highlighted = false;
                this.sessions[sessionIndex].newMessagesCount = 0;
                this.displayedSessions = [...this.sessions];
            }

            this.stopPolling();
            this.selectedSessionId = sessionId;
            this.lastMsgId = 0;
            this.messagesPage = 1;
            this.hasMoreMessages = false;
            this.messages = [];

            // Load messages for this session
            try {
                const url = `{{ route('bots.load-more-messages', $bot) }}?session_id=${encodeURIComponent(sessionId)}&page=1&per_page=${this.messagesPerPage}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data && data.messages) {
                    this.messages = data.messages.map(msg => ({
                        id: msg.id,
                        session_id: msg.session_id,
                        role: msg.role,
                        content: msg.content,
                        created_at: msg.created_at,
                    })).reverse();

                    this.hasMoreMessages = data.has_more;
                    this.messagesPage = 1;

                    // Update last message ID
                    if (this.messages.length > 0) {
                        this.lastMsgId = Math.max(...this.messages.map(m => m.id));
                    }
                }
            } catch (error) {
                console.error('Load messages error:', error);
            }

            this.startPolling();
            this.scrollToBottom();

            // Update URL without reload
            const url = new URL(window.location.href);
            url.searchParams.set('session_id', sessionId);
            window.history.pushState({}, '', url);

            // Reset manual selection flag after 5 seconds
            setTimeout(() => {
                if (!this.isComponentDestroyed) {
                    this.userManuallySelectedSession = false;
                }
            }, 5000);
        },

        async jumpToSession(sessionId) {
            if (sessionId === this.selectedSessionId || this.isComponentDestroyed) return;
            this.selectSession(sessionId);
        },

        checkForMoreMessages() {
            this.hasMoreMessages = this.messages.length >= this.messagesPerPage;
        },

        scrollToBottom() {
            setTimeout(() => {
                const container = document.getElementById('chat-messages');
                if (container && !this.isComponentDestroyed) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 100);
        },

        formatTime(date, isTimeOnly = false) {
            if (!date) return 'Just now';
            try {
                const d = new Date(date);
                if (isTimeOnly) {
                    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
                return d.toLocaleString();
            } catch {
                return 'Just now';
            }
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
                    // Remove session from list
                    const index = this.sessions.findIndex(s => s.session_id === sessionId);
                    if (index !== -1) {
                        this.sessions.splice(index, 1);
                        this.displayedSessions = [...this.sessions];
                    }

                    if (this.selectedSessionId === sessionId) {
                        this.stopPolling();
                        this.selectedSessionId = null;
                        this.messages = [];
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
                    this.sessions = [];
                    this.displayedSessions = [];
                    this.sessionMessageCounts.clear();
                    this.newSessionsCount = 0;
                    this.stopPolling();
                    this.selectedSessionId = null;
                    this.messages = [];

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
        }
    };
}
</script>
@endpush
@endsection
