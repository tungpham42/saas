@extends('layouts.app')

@section('title', 'Leads - ' . $bot->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <div class="flex items-center gap-4">
                <a href="{{ route('bots.show', $bot) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Customer Leads</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Information collected from pre-chat forms</p>
                </div>
            </div>
        </div>

        @if($leads->count() > 0)
        <a href="{{ route('bots.leads.export', $bot) }}"
           class="btn-secondary px-5 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
            <i class="fas fa-download"></i>
            <span>Export CSV</span>
        </a>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">Total Leads</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($leads->total()) }}</p>
                </div>
                <i class="fas fa-users text-3xl text-white/50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-5 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">This Month</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($leads->where('created_at', '>=', now()->startOfMonth())->count()) }}</p>
                </div>
                <i class="fas fa-calendar-alt text-3xl text-white/50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">This Week</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($leads->where('created_at', '>=', now()->startOfWeek())->count()) }}</p>
                </div>
                <i class="fas fa-chart-line text-3xl text-white/50"></i>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Channel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-8 h-8 flex items-center justify-center">
                                    <i class="fas fa-user text-green-600 dark:text-green-400 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $lead->customer_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-900 dark:text-white">{{ $lead->customer_phone }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $channelInfo = parseLeadChannel($lead->session_id, $bot);
                            @endphp
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm">
                                <span>{{ $channelInfo['icon'] }}</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $channelInfo['name'] }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $lead->created_at->format('M d, Y H:i') }}</div>
                            <div class="text-xs text-gray-500">{{ $lead->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('bots.history', $bot) }}?session_id={{ urlencode($lead->session_id) }}"
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium inline-flex items-center gap-1">
                                View Chat <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-6xl mb-4 opacity-50">👥</div>
                            <p class="text-gray-600 dark:text-gray-400">No leads captured yet</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Enable pre-chat form in bot settings to collect customer information</p>
                            <div class="mt-4">
                                <a href="{{ route('bots.show', $bot) }}?tab=settings"
                                   class="inline-flex items-center gap-2 btn-primary px-5 py-2 rounded-xl text-white text-sm">
                                    <i class="fas fa-cog"></i>
                                    <span>Configure Pre-chat Form</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leads->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $leads->links() }}
        </div>
        @endif
    </div>
</div>

@php
function parseLeadChannel($sessionId, $bot) {
    $parts = explode('__', $sessionId);

    $icons = ['fb' => '📘', 'zalo' => '🔵', 'tt' => '🎵', 'sp' => '🟠', 'zlpn' => '👤', 'wa' => '🟩'];
    $names = ['fb' => 'Facebook', 'zalo' => 'Zalo OA', 'tt' => 'TikTok', 'sp' => 'Shopee', 'zlpn' => 'Zalo Personal', 'wa' => 'WhatsApp'];

    if (count($parts) === 3) {
        $channelType = $parts[0];
        return ['icon' => $icons[$channelType] ?? '💬', 'name' => $names[$channelType] ?? ucfirst($channelType)];
    }
    return ['icon' => '🌐', 'name' => 'Website'];
}
@endphp
@endsection
