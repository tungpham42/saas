@extends('layouts.app')

@section('title', 'Configure: ' . $bot->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div class="flex items-center gap-4">
            <a href="{{ route('bots.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $bot->name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Configure your AI assistant</p>
            </div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-700 rounded-xl px-4 py-2">
            <code class="text-xs font-mono text-gray-600 dark:text-gray-300">{{ substr($bot->api_key, 0, 30) }}...</code>
            <button onclick="copyToClipboard('{{ $bot->api_key }}', this)" class="ml-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-2">
                    <i class="fas fa-comments text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Sessions</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalSessions) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-2">
                    <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Leads</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalLeads) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 dark:bg-purple-900/30 rounded-lg p-2">
                    <i class="fas fa-envelope text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Messages</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalMessages) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700 animate-fade-in-up" style="animation-delay: 0.2s">
        <nav class="flex flex-wrap gap-1 -mb-px">
            <a href="{{ route('bots.show', $bot) }}?tab=settings"
               class="px-5 py-3 text-sm font-medium rounded-t-lg transition-all {{ $tab === 'settings' ? 'gradient-primary text-white shadow-lg' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fas fa-sliders-h mr-2"></i>AI Settings
            </a>
            <a href="{{ route('bots.show', $bot) }}?tab=channels"
               class="px-5 py-3 text-sm font-medium rounded-t-lg transition-all {{ $tab === 'channels' ? 'gradient-primary text-white shadow-lg' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fab fa-facebook-messenger mr-2"></i>Social Channels
            </a>
            <a href="{{ route('bots.show', $bot) }}?tab=rag"
               class="px-5 py-3 text-sm font-medium rounded-t-lg transition-all {{ $tab === 'rag' ? 'gradient-primary text-white shadow-lg' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fas fa-database mr-2"></i>Knowledge Base
            </a>
            <a href="{{ route('bots.live-chat', $bot) }}"
               class="px-5 py-3 text-sm font-medium rounded-t-lg transition-all text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800">
                <i class="fas fa-comment-dots mr-2"></i>Live Chat
            </a>
            <a href="{{ route('bots.history', $bot) }}"
               class="px-5 py-3 text-sm font-medium rounded-t-lg transition-all text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800">
                <i class="fas fa-history mr-2"></i>History
            </a>
            <a href="{{ route('bots.leads', $bot) }}"
               class="px-5 py-3 text-sm font-medium rounded-t-lg transition-all text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800">
                <i class="fas fa-user-plus mr-2"></i>Leads
            </a>
            <a href="{{ route('bots.statistics', $bot) }}"
               class="px-5 py-3 text-sm font-medium rounded-t-lg transition-all text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800">
                <i class="fas fa-chart-line mr-2"></i>Analytics
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
}
</script>
@endpush
@endsection
