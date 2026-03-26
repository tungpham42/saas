@extends('layouts.app')

@section('title', 'Care for ' . $bot->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 animate-gentle">
        <div class="flex items-center gap-4">
            <a href="{{ route('bots.index') }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 transition">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <div class="gradient-warm rounded-xl p-2 shadow-lg">
                        <i class="fas fa-robot text-amber-900 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-amber-800 dark:text-amber-200">{{ $bot->name }}</h1>
                        <p class="text-amber-600 dark:text-amber-400 text-sm mt-1">Your AI companion, ready to help</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-amber-50 dark:bg-gray-800 rounded-2xl px-4 py-2 flex items-center gap-2">
            <i class="fas fa-key text-amber-500 text-xs"></i>
            <code class="text-xs font-mono text-amber-700 dark:text-amber-300">{{ substr($bot->api_key, 0, 30) }}...</code>
            <button onclick="copyToClipboard('{{ $bot->api_key }}', this)"
                    class="text-amber-500 hover:text-amber-600 transition">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-gentle" style="animation-delay: 0.1s">
        <div class="card-warm p-4">
            <div class="flex items-center gap-3">
                <div class="bg-amber-100 dark:bg-amber-900/30 rounded-xl p-2">
                    <i class="fas fa-comments text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-xs text-amber-500">Conversations</p>
                    <p class="text-xl font-bold text-amber-800 dark:text-amber-200">{{ number_format($totalSessions) }}</p>
                </div>
            </div>
        </div>
        <div class="card-warm p-4">
            <div class="flex items-center gap-3">
                <div class="bg-amber-100 dark:bg-amber-900/30 rounded-xl p-2">
                    <i class="fas fa-heart text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-xs text-amber-500">New Friends</p>
                    <p class="text-xl font-bold text-amber-800 dark:text-amber-200">{{ number_format($totalLeads) }}</p>
                </div>
            </div>
        </div>
        <div class="card-warm p-4">
            <div class="flex items-center gap-3">
                <div class="bg-amber-100 dark:bg-amber-900/30 rounded-xl p-2">
                    <i class="fas fa-envelope text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-xs text-amber-500">Messages</p>
                    <p class="text-xl font-bold text-amber-800 dark:text-amber-200">{{ number_format($totalMessages) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-amber-200 dark:border-gray-700 animate-gentle" style="animation-delay: 0.2s">
        <nav class="flex flex-wrap gap-1 -mb-px">
            <a href="{{ route('bots.show', $bot) }}?tab=settings"
               class="px-5 py-3 text-sm font-medium rounded-t-xl transition-all {{ $tab === 'settings' ? 'gradient-warm text-amber-900 shadow-md' : 'text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-800' }}">
                <i class="fas fa-sliders-h mr-2"></i>Personality
            </a>
            <a href="{{ route('bots.show', $bot) }}?tab=channels"
               class="px-5 py-3 text-sm font-medium rounded-t-xl transition-all {{ $tab === 'channels' ? 'gradient-warm text-amber-900 shadow-md' : 'text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-800' }}">
                <i class="fab fa-facebook-messenger mr-2"></i>Connect
            </a>
            <a href="{{ route('bots.show', $bot) }}?tab=rag"
               class="px-5 py-3 text-sm font-medium rounded-t-xl transition-all {{ $tab === 'rag' ? 'gradient-warm text-amber-900 shadow-md' : 'text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-800' }}">
                <i class="fas fa-brain mr-2"></i>Teach
            </a>
            <a href="{{ route('bots.live-chat', $bot) }}"
               class="px-5 py-3 text-sm font-medium rounded-t-xl transition-all text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-800">
                <i class="fas fa-comment-dots mr-2"></i>Live Chat
            </a>
            <a href="{{ route('bots.history', $bot) }}"
               class="px-5 py-3 text-sm font-medium rounded-t-xl transition-all text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-800">
                <i class="fas fa-history mr-2"></i>History
            </a>
            <a href="{{ route('bots.leads', $bot) }}"
               class="px-5 py-3 text-sm font-medium rounded-t-xl transition-all text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-800">
                <i class="fas fa-user-friends mr-2"></i>Friends
            </a>
            <a href="{{ route('bots.statistics', $bot) }}"
               class="px-5 py-3 text-sm font-medium rounded-t-xl transition-all text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-800">
                <i class="fas fa-chart-line mr-2"></i>Stories
            </a>
        </nav>
    </div>

    <!-- Tab Content -->
    <div>
        @if($tab === 'settings')
            @include('bots.tabs.settings')
        @elseif($tab === 'channels')
            @include('bots.tabs.channels')
        @elseif($tab === 'rag')
            @include('bots.tabs.rag')
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text);
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check text-green-500"></i>';
    setTimeout(() => {
        button.innerHTML = originalHtml;
    }, 2000);

    Swal.fire({
        icon: 'success',
        title: 'Copied! 📋',
        text: 'API Key copied to clipboard',
        toast: true,
        timer: 2000,
        showConfirmButton: false,
        position: 'top-end'
    });
}
</script>
@endpush
@endsection
