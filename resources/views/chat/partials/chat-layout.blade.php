<div x-data="chatManager()" x-init="init()" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
    <div class="flex flex-col lg:flex-row h-[calc(100vh-200px)] min-h-[500px]">
        <!-- Sidebar - Sessions List -->
        <div class="w-full lg:w-80 border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50 dark:bg-gray-900/50">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-comments text-blue-500"></i>
                    <span>{{ $isLive ? 'Live Sessions' : 'Chat History' }}</span>
                </h3>

                @if(!$isLive)
                <form action="{{ route('bots.clear-all-chats', $bot) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" onclick="return confirmClearAll()"
                            class="w-full px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/30 transition flex items-center justify-center gap-2">
                        <i class="fas fa-trash-alt"></i>
                        <span>Clear All History</span>
                    </button>
                </form>
                @endif

                <!-- Date Filter -->
                <form method="GET" class="mt-3 space-y-2" id="filter-form">
                    <input type="hidden" name="tab" value="{{ $isLive ? 'live-chat' : 'history' }}">
                    <div class="relative">
                        <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <select name="date_preset" onchange="this.form.submit()"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="" {{ !$datePreset ? 'selected' : '' }}>All Time</option>
                            <option value="today" {{ $datePreset === 'today' ? 'selected' : '' }}>Today so far</option>
                            <option value="yesterday" {{ $datePreset === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="last_7" {{ $datePreset === 'last_7' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="this_month" {{ $datePreset === 'this_month' ? 'selected' : '' }}>This month</option>
                            <option value="last_month" {{ $datePreset === 'last_month' ? 'selected' : '' }}>Last month</option>
                            <option value="last_30" {{ $datePreset === 'last_30' ? 'selected' : '' }}>Last 30 days</option>
                            <option value="custom" {{ $datePreset === 'custom' ? 'selected' : '' }}>Custom Date...</option>
                        </select>
                    </div>

                    <input type="date" name="filter_date" value="{{ $filterDate }}"
                           onchange="this.form.submit()"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm {{ $datePreset === 'custom' ? '' : 'hidden' }}"
                           id="custom_date_input">
                </form>
            </div>

            <!-- Sessions List -->
            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                @forelse($sessions as $session)
                <?php
                    $isActive = $selectedSession === $session->session_id;
                    $sessionInfo = parseSessionId($session->session_id, $bot);
                    $lastTime = \Carbon\Carbon::parse($session->last_time);
                ?>
                <a href="{{ route($isLive ? 'bots.live-chat' : 'bots.history', $bot) }}?session_id={{ urlencode($session->session_id) }}&date_preset={{ $datePreset }}&filter_date={{ $filterDate }}"
                   class="block p-3 rounded-xl transition-all {{ $isActive ? 'gradient-primary text-white shadow-md' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $sessionInfo['icon'] ?? '💬' }}</span>
                                <span class="font-mono text-xs truncate {{ $isActive ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ substr($session->session_id, 0, 20) }}...
                                </span>
                            </div>
                            @if(isset($sessionInfo['channel_name']))
                            <p class="text-xs {{ $isActive ? 'text-white/80' : 'text-gray-400' }} mt-1">
                                <i class="fas fa-link mr-1"></i>{{ $sessionInfo['channel_name'] }}
                            </p>
                            @endif
                            <div class="flex items-center gap-3 mt-2 text-xs {{ $isActive ? 'text-white/70' : 'text-gray-400' }}">
                                <span><i class="far fa-clock mr-1"></i>{{ $lastTime->format('M d, H:i') }}</span>
                                <span><i class="fas fa-comment mr-1"></i>{{ $session->msgs }} msgs</span>
                            </div>
                        </div>
                        @if($isLive && !hasRecentAdminReply($bot, $session->session_id))
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        @endif
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                    <p>No sessions found</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col bg-white dark:bg-gray-800">
            @if($selectedSession && $messages && count($messages) > 0)
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-wrap justify-between items-center gap-3 sticky top-0 z-10">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center">
                            <i class="fas fa-comment-dots text-white text-xs"></i>
                        </div>
                        <div>
                            <span class="font-mono text-sm text-gray-600 dark:text-gray-300">{{ substr($selectedSession, 0, 25) }}...</span>
                            @if(isset($sessionInfo) && isset($sessionInfo['channel_name']))
                                <span class="ml-2 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-full text-xs">{{ $sessionInfo['channel_name'] }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('bots.export-session', $bot) }}?session_id={{ urlencode($selectedSession) }}"
                           class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition flex items-center gap-1">
                            <i class="fas fa-download"></i>
                            <span>Export</span>
                        </a>
                        @if(!$isLive)
                        <form action="{{ route('bots.clear-session', $bot) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="session_id" value="{{ $selectedSession }}">
                            <button type="submit" onclick="return confirmClearSession()"
                                    class="px-3 py-1.5 text-sm bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition flex items-center gap-1">
                                <i class="fas fa-trash-alt"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
                    @foreach($messages as $msg)
                        @include('chat.partials.message-bubble', ['message' => $msg])
                    @endforeach
                </div>

                <!-- Reply Form (Live Chat Only) -->
                @if($isLive)
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <form id="reply-form" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $selectedSession }}">
                        <div class="flex-1 relative">
                            <input type="text" name="message" id="reply-message"
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   placeholder="Type a message to reply..." autocomplete="off">
                            <button type="submit"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            @elseif($selectedSession && (!$messages || count($messages) == 0))
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center p-8">
                        <div class="text-6xl mb-4 opacity-50">💬</div>
                        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300">No Messages Yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Start a conversation to see messages here</p>
                    </div>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center p-8">
                        <div class="text-6xl mb-4 opacity-50">💭</div>
                        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300">Select a Conversation</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Choose a session from the sidebar to view messages</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function chatManager() {
    return {
        lastMsgId: {{ $messages && count($messages) > 0 ? ($messages->last()->id ?? 0) : 0 }},
        pollingInterval: null,
        isPolling: false,

        init() {
            @if($isLive && $selectedSession)
                this.startPolling();
            @endif

            // Auto-scroll to bottom
            const container = document.getElementById('chat-messages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }

            // Reply form handler
            const form = document.getElementById('reply-form');
            if (form) {
                form.addEventListener('submit', (e) => this.sendReply(e));
            }
        },

        startPolling() {
            if (this.pollingInterval) clearInterval(this.pollingInterval);
            this.pollingInterval = setInterval(() => this.pollMessages(), 2000);
        },

        async pollMessages() {
            if (this.isPolling) return;
            this.isPolling = true;

            try {
                const response = await fetch(`{{ route('bots.poll', $bot) }}?session_id={{ $selectedSession }}&last_id=${this.lastMsgId}`);
                const data = await response.json();

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        if (msg.id > this.lastMsgId) {
                            this.appendMessage(msg);
                            this.lastMsgId = msg.id;
                        }
                    });
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

            const messageHtml = this.renderMessage(message);
            container.insertAdjacentHTML('beforeend', messageHtml);
            container.scrollTop = container.scrollHeight;
        },

        renderMessage(message) {
            const isUser = message.role === 'user';
            const isAdmin = message.role === 'admin';
            const align = isUser ? 'justify-end' : 'justify-start';
            const bgClass = isUser ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white' :
                           (isAdmin ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' : 'bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600');
            const roleLabel = isUser ? 'You' : (isAdmin ? 'Admin' : 'AI Assistant');
            const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            return `
                <div class="flex ${align} animate-fade-in-up">
                    <div class="max-w-[75%] ${bgClass} rounded-2xl p-3 shadow-sm">
                        <div class="flex items-center gap-2 text-xs ${isUser ? 'text-blue-100' : (isAdmin ? 'text-green-100' : 'text-gray-500')} mb-1">
                            <i class="fas ${isUser ? 'fa-user' : (isAdmin ? 'fa-user-tie' : 'fa-robot')}"></i>
                            <span>${roleLabel}</span>
                            <span>•</span>
                            <span>${time}</span>
                        </div>
                        <div class="text-sm whitespace-pre-wrap break-words">${this.escapeHtml(message.content)}</div>
                    </div>
                </div>
            `;
        },

        async sendReply(event) {
            event.preventDefault();

            const input = document.getElementById('reply-message');
            const message = input.value.trim();
            if (!message) return;

            input.disabled = true;

            try {
                const response = await fetch('{{ route('bots.send-reply', $bot) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        session_id: '{{ $selectedSession }}',
                        message: message
                    })
                });

                if (response.ok) {
                    input.value = '';
                    // Message will appear via polling
                }
            } catch (error) {
                console.error('Send error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Send',
                    text: 'Could not send message. Please try again.',
                    confirmButtonColor: '#3b82f6'
                });
            } finally {
                input.disabled = false;
                input.focus();
            }
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }
}

function confirmClearAll() {
    Swal.fire({
        title: 'Clear All History?',
        text: 'This will permanently delete all chat history for this bot. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, clear everything!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.querySelector('form[action*="clear-all-chats"]').submit();
        }
    });
    return false;
}

function confirmClearSession() {
    Swal.fire({
        title: 'Delete Session?',
        text: 'This chat session will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            return true;
        }
    });
    return false;
}
</script>
@endpush

@php
function parseSessionId($sessionId, $bot) {
    $parts = explode('__', $sessionId);

    $icons = ['fb' => '📘', 'zalo' => '🔵', 'tt' => '🎵', 'sp' => '🟠', 'zlpn' => '👤', 'wa' => '🟩'];

    if (count($parts) === 3) {
        $channelType = $parts[0];
        $channelId = (int) $parts[1];
        $channel = $bot->channels()->find($channelId);

        return [
            'type' => $channelType,
            'icon' => $icons[$channelType] ?? '💬',
            'channel_name' => $channel?->channel_name,
            'user_id' => $parts[2],
        ];
    }

    return [
        'type' => 'web',
        'icon' => '🌐',
        'channel_name' => 'Website',
    ];
}

function hasRecentAdminReply($bot, $sessionId) {
    $timeoutMins = $bot->admin_timeout_mins ?? 15;
    return $bot->chatLogs()
        ->where('session_id', $sessionId)
        ->where('role', 'admin')
        ->where('created_at', '>=', now()->subMinutes($timeoutMins))
        ->exists();
}
@endphp
