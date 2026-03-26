<div x-data="chatManager()" x-init="init()" class="card-warm overflow-hidden">
    <div class="flex flex-col lg:flex-row h-[calc(100vh-200px)] min-h-[500px]">
        <!-- Sidebar - Sessions List -->
        <div class="w-full lg:w-80 border-r border-amber-100 dark:border-gray-700 flex flex-col bg-amber-50/30 dark:bg-gray-800/30">
            <div class="p-4 border-b border-amber-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                <h3 class="font-bold text-amber-800 dark:text-amber-200 flex items-center gap-2">
                    <i class="fas fa-comments text-amber-500"></i>
                    <span>{{ $isLive ? '💬 Live Chats' : '📜 Chat History' }}</span>
                </h3>

                @if(!$isLive)
                <form action="{{ route('bots.clear-all-chats', $bot) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" onclick="return confirmClearAll()"
                            class="w-full px-3 py-2 bg-amber-50 dark:bg-red-900/20 text-amber-600 dark:text-red-400 rounded-xl text-sm font-medium hover:bg-amber-100 dark:hover:bg-red-900/30 transition flex items-center justify-center gap-2">
                        <i class="fas fa-broom"></i>
                        <span>Tidy Up All</span>
                    </button>
                </form>
                @endif

                <!-- Date Filter -->
                <form method="GET" class="mt-3 space-y-2">
                    <input type="hidden" name="tab" value="{{ $isLive ? 'live-chat' : 'history' }}">
                    <div class="relative">
                        <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-amber-400 text-sm"></i>
                        <select name="date_preset" onchange="this.form.submit()"
                                class="w-full pl-10 pr-4 py-2 border border-amber-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-amber-800 dark:text-amber-200 text-sm focus:ring-2 focus:ring-amber-500">
                            <option value="" {{ !$datePreset ? 'selected' : '' }}>All Time</option>
                            <option value="today" {{ $datePreset === 'today' ? 'selected' : '' }}>Today so far</option>
                            <option value="yesterday" {{ $datePreset === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="last_7" {{ $datePreset === 'last_7' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="this_month" {{ $datePreset === 'this_month' ? 'selected' : '' }}>This month</option>
                            <option value="last_month" {{ $datePreset === 'last_month' ? 'selected' : '' }}>Last month</option>
                            <option value="last_30" {{ $datePreset === 'last_30' ? 'selected' : '' }}>Last 30 days</option>
                            <option value="custom" {{ $datePreset === 'custom' ? 'selected' : '' }}>Pick a date...</option>
                        </select>
                    </div>

                    <input type="date" name="filter_date" value="{{ $filterDate }}"
                           onchange="this.form.submit()"
                           class="w-full px-4 py-2 border border-amber-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-amber-800 dark:text-amber-200 text-sm {{ $datePreset === 'custom' ? '' : 'hidden' }}">
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
                   class="block p-3 rounded-xl transition-all {{ $isActive ? 'gradient-warm text-amber-900 shadow-md' : 'hover:bg-amber-50 dark:hover:bg-gray-800' }}">
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
                                <span><i class="far fa-clock mr-1"></i>{{ $lastTime->format('M d, H:i') }}</span>
                                <span><i class="fas fa-comment mr-1"></i>{{ $session->msgs }} msgs</span>
                            </div>
                        </div>
                        @if($isLive && !hasRecentAdminReply($bot, $session->session_id))
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse-soft"></div>
                        @endif
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

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col bg-white dark:bg-gray-800">
            @if($selectedSession && $messages && count($messages) > 0)
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
                        <form action="{{ route('bots.clear-session', $bot) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="session_id" value="{{ $selectedSession }}">
                            <button type="submit" onclick="return confirmClearSession()"
                                    class="px-3 py-1.5 text-sm bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-xl transition flex items-center gap-1">
                                <i class="fas fa-trash-alt"></i>
                                <span>Clear</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-amber-50/30 to-white dark:from-gray-900 dark:to-gray-800">
                    @foreach($messages as $msg)
                        @include('chat.partials.message-bubble', ['message' => $msg])
                    @endforeach
                </div>

                <!-- Reply Form -->
                @if($isLive)
                <div class="p-4 border-t border-amber-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <form id="reply-form" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $selectedSession }}">
                        <div class="flex-1 relative">
                            <input type="text" name="message" id="reply-message"
                                   class="w-full px-4 py-3 pr-12 border border-amber-200 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-amber-500 focus:border-transparent bg-white dark:bg-gray-700 text-amber-800 dark:text-amber-200"
                                   placeholder="Type a warm reply..." autocomplete="off">
                            <button type="submit"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-1.5 btn-soft rounded-xl text-sm">
                                Send 💝
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            @elseif($selectedSession && (!$messages || count($messages) == 0))
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center p-8">
                        <div class="text-6xl mb-4">💭</div>
                        <h3 class="text-xl font-semibold text-amber-700 dark:text-amber-300">Start the Conversation</h3>
                        <p class="text-amber-500 dark:text-amber-400 mt-2">Be the first to say hello!</p>
                    </div>
                </div>
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

            const container = document.getElementById('chat-messages');
            if (container) container.scrollTop = container.scrollHeight;

            const form = document.getElementById('reply-form');
            if (form) form.addEventListener('submit', (e) => this.sendReply(e));
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
            const bgClass = isUser ? 'gradient-warm text-amber-900' :
                           (isAdmin ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-white dark:bg-gray-700 border border-amber-200 dark:border-gray-600 text-amber-800 dark:text-amber-200');
            const roleLabel = isUser ? 'You' : (isAdmin ? '✨ Admin' : '🤖 AI Assistant');
            const roleIcon = isUser ? 'fa-user' : (isAdmin ? 'fa-user-tie' : 'fa-robot');
            const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            return `
                <div class="flex ${align} animate-gentle">
                    <div class="max-w-[75%] ${bgClass} rounded-2xl p-3 shadow-sm">
                        <div class="flex items-center gap-2 text-xs opacity-70 mb-1">
                            <i class="fas ${roleIcon}"></i>
                            <span>${roleLabel}</span>
                            <span>•</span>
                            <span>${time}</span>
                        </div>
                        <div class="text-sm whitespace-pre-wrap break-words leading-relaxed">${this.escapeHtml(message.content)}</div>
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
                await fetch('{{ route('bots.send-reply', $bot) }}', {
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
                input.value = '';
            } catch (error) {
                Swal.fire('Oops!', 'Could not send message. Try again?', 'error');
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
        title: 'Tidy up all chats? 🧹',
        text: 'All conversations will be cleaned. This is permanent!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, clean up',
        cancelButtonText: 'Keep them'
    }).then((result) => {
        if (result.isConfirmed) document.querySelector('form[action*="clear-all-chats"]').submit();
    });
    return false;
}

function confirmClearSession() {
    Swal.fire({
        title: 'Clear this chat?',
        text: 'This conversation will be removed.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, clear it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) return true;
    });
    return false;
}
</script>
@endpush
