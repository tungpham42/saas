@extends('layouts.app')

@section('title', __('Leads') . ' - ' . $bot->name)

@section('content')
<div class="space-y-8">
    <div class="flex flex-wrap items-center justify-between gap-4 animate-gentle">
        <div>
            <div class="flex items-center gap-4">
                <a href="{{ route('bots.show', $bot) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">{{ __('New Friends 👥') }}</h1>
                    <p class="text-amber-600 dark:text-amber-400 mt-1">{{ __('Wonderful people who reached out to say hello') }}</p>
                </div>
            </div>
        </div>

        @if($leads->count() > 0)
        <a href="{{ route('bots.leads.export', $bot) }}"
           class="btn-outline-soft inline-flex items-center gap-2">
            <i class="fas fa-download"></i>
            <span>{{ __('Save Friends List') }}</span>
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card-warm p-5 animate-gentle" style="animation-delay: 0.1s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm">{{ __('Total Friends') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-1">{{ number_format($leads->total()) }}</p>
                </div>
                <i class="fas fa-users text-3xl text-amber-400"></i>
            </div>
        </div>
        <div class="card-warm p-5 animate-gentle" style="animation-delay: 0.2s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm">{{ __('This Month') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-1">{{ number_format($leads->where('created_at', '>=', now()->startOfMonth())->count()) }}</p>
                </div>
                <i class="fas fa-calendar-alt text-3xl text-amber-400"></i>
            </div>
        </div>
        <div class="card-warm p-5 animate-gentle" style="animation-delay: 0.3s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm">{{ __('This Week') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-1">{{ number_format($leads->where('created_at', '>=', now()->startOfWeek())->count()) }}</p>
                </div>
                <i class="fas fa-chart-line text-3xl text-amber-400"></i>
            </div>
        </div>
    </div>

    <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.4s">
        <div class="overflow-x-auto">
            <table class="table-warm">
                <thead>
                    <tr>
                        <th>{{ __('Friend') }}</th>
                        <th>{{ __('Contact') }}</th>
                        <th>{{ __('Channel') }}</th>
                        <th>{{ __('When') }}</th>
                        <th>{{ __('Chat') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr class="hover:bg-amber-50 dark:hover:bg-gray-800 transition-colors">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="gradient-warm rounded-full w-10 h-10 flex items-center justify-center">
                                    <i class="fas fa-user text-amber-900 text-sm"></i>
                                </div>
                                <span class="font-medium text-amber-800 dark:text-amber-200">{{ $lead->customer_name }}</span>
                            </div>
                        </td>
                        <td class="text-amber-700 dark:text-amber-300">
                            <i class="fas fa-phone-alt mr-2 text-amber-400"></i>
                            {{ $lead->customer_phone }}
                        </td>
                        <td>
                            @php
                                $channelInfo = parseLeadChannel($lead->session_id, $bot);
                            @endphp
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 dark:bg-gray-700 rounded-full text-sm">
                                <span>{{ $channelInfo['icon'] }}</span>
                                <span class="text-amber-700 dark:text-amber-300">{{ $channelInfo['name'] }}</span>
                            </span>
                        </td>
                        <td>
                            <div class="text-sm text-amber-700 dark:text-amber-300">{{ $lead->created_at->format('M d, Y H:i') }}</div>
                            <div class="text-xs text-amber-400">{{ $lead->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <a href="{{ route('bots.history', $bot) }}?session_id={{ urlencode($lead->session_id) }}"
                               class="btn-outline-soft px-3 py-1.5 text-sm inline-flex items-center gap-1">
                                {{ __('Chat') }} <i class="fas fa-heart text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <div class="text-6xl mb-4">🤗</div>
                            <p class="text-amber-600 dark:text-amber-400">{{ __('No friends yet') }}</p>
                            <p class="text-sm text-amber-500 dark:text-amber-500 mt-1">{{ __('Enable pre-chat form to meet new people') }}</p>
                            <div class="mt-4">
                                <a href="{{ route('bots.show', $bot) }}?tab=settings"
                                   class="btn-soft inline-flex items-center gap-2 text-sm">
                                    <i class="fas fa-heart"></i>
                                    <span>{{ __('Enable Welcome Form') }}</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
        <div class="px-6 py-4 border-t border-amber-100 dark:border-gray-700">
            {{ $leads->links() }}
        </div>
        @endif
    </div>
</div>

@php
function parseLeadChannel($sessionId, $bot) {
    $parts = explode('__', $sessionId);
    $icons = ['fb' => '📘', 'zalo' => '🔵', 'tt' => '🎵', 'sp' => '🟠', 'zlpn' => '👤', 'wa' => '🟩'];
    $names = ['fb' => 'Facebook', 'zalo' => 'Zalo OA', 'tt' => 'TikTok', 'sp' => 'Shopee', 'zlpn' => __('Zalo Personal'), 'wa' => 'WhatsApp'];
    if (count($parts) === 3) {
        $channelType = $parts[0];
        return ['icon' => $icons[$channelType] ?? '💬', 'name' => $names[$channelType] ?? ucfirst($channelType)];
    }
    return ['icon' => '🌐', 'name' => __('Website')];
}
@endphp
@endsection
