@extends('layouts.app')

@section('title', __('Dashboard - SaaS AI Chatbot'))

@section('content')
<div class="space-y-8">
    <div class="gradient-warm rounded-2xl p-8 text-amber-900 shadow-xl animate-gentle relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ __('Welcome back') }}, {{ $user->name }}! 👋</h1>
            <p class="text-amber-800 dark:text-amber-100 text-lg">{{ __('Ready to help more customers today? Your bots are waiting to assist!') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm font-medium">{{ __('Total Messages') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ number_format($totalMessages) }}</p>
                </div>
                <div class="gradient-warm rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                    <i class="fas fa-comments text-amber-900 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-600 dark:text-amber-400">
                <i class="fas fa-chart-line mr-1"></i> {{ __('Lifetime conversations') }}
            </div>
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm font-medium">{{ __('Total Leads') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ number_format($totalLeads) }}</p>
                </div>
                <div class="gradient-warm rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-amber-900 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-600 dark:text-amber-400">
                <i class="fas fa-user-plus mr-1"></i> {{ __('Happy customers') }}
            </div>
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm font-medium">{{ __('Active Bots') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ $botCount }}</p>
                </div>
                <div class="gradient-warm rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                    <i class="fas fa-robot text-amber-900 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-600 dark:text-amber-400">
                <i class="fas fa-chart-simple mr-1"></i> {{ __('Active assistants') }}
            </div>
        </div>
    </div>

    @if(count($botsUsage) > 0)
    <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.4s">
        <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('Your Bot Family 🤖') }}</h3>
            <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">{{ __('Your amazing AI assistants at a glance') }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="table-warm">
                <thead>
                    <tr>
                        <th>{{ __('Bot Name') }}</th>
                        <th>{{ __('Messages') }}</th>
                        <th>{{ __('Leads') }}</th>
                        <th>{{ __('Sessions') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($botsUsage as $item)
                    <tr>
                        <td class="font-medium">{{ $item['bot']->name }}</td>
                        <td>{{ number_format($item['messages']) }}</td>
                        <td>{{ number_format($item['leads']) }}</td>
                        <td>{{ number_format($item['sessions']) }}</td>
                        <td>
                            <a href="{{ route('bots.show', $item['bot']) }}" class="text-amber-600 hover:text-amber-700 font-medium inline-flex items-center gap-1">
                                {{ __('Visit') }} <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.5s">
            <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('Recent Chats 💬') }}</h3>
                <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">{{ __('Latest conversations with customers') }}</p>
            </div>
            <div class="divide-y divide-amber-100 dark:divide-gray-700 max-h-96 overflow-y-auto">
                @forelse($recentSessions as $session)
                <div class="p-4 hover:bg-amber-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-comment-dots text-amber-500 text-sm"></i>
                                <code class="text-xs font-mono text-amber-700 dark:text-amber-300">{{ substr($session->session_id, 0, 25) }}...</code>
                            </div>
                            <div class="flex items-center gap-3 mt-2 text-xs text-amber-500 dark:text-amber-400">
                                <span><i class="far fa-calendar-alt mr-1"></i> {{ $session->created_at->format('M d, Y H:i') }}</span>
                                <span><i class="fas fa-reply-all mr-1"></i> {{ $session->admin_msg_count }} {{ __('replies') }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="status-badge {{ $session->admin_msg_count > 0 ? 'status-active' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                <i class="fas {{ $session->admin_msg_count > 0 ? 'fa-user-check' : 'fa-robot' }}"></i>
                                {{ $session->admin_msg_count > 0 ? __('Human helped') : __('AI only') }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-amber-500 dark:text-amber-400">
                    <i class="fas fa-comments text-4xl mb-3 opacity-50"></i>
                    <p>{{ __('No recent sessions') }}</p>
                    <p class="text-sm mt-1">{{ __('Start a conversation to see it here') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.6s">
            <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('New Friends 🤝') }}</h3>
                <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">{{ __('Recent leads who reached out') }}</p>
            </div>
            <div class="divide-y divide-amber-100 dark:divide-gray-700 max-h-96 overflow-y-auto">
                @forelse($recentLeads as $lead)
                <div class="p-4 hover:bg-amber-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <div class="gradient-warm rounded-full w-8 h-8 flex items-center justify-center">
                                    <i class="fas fa-user text-amber-900 text-xs"></i>
                                </div>
                                <span class="font-medium text-amber-800 dark:text-amber-200">{{ $lead->customer_name }}</span>
                            </div>
                            <div class="flex items-center gap-3 mt-2 text-xs text-amber-500 dark:text-amber-400">
                                <span><i class="fas fa-phone-alt mr-1"></i> {{ $lead->customer_phone }}</span>
                                <span><i class="far fa-clock mr-1"></i> {{ $lead->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('bots.history', $lead->bot_id) }}?session_id={{ urlencode($lead->session_id) }}"
                           class="btn-outline-soft px-3 py-1 text-sm inline-flex items-center gap-1">
                            {{ __('Chat') }} <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-amber-500 dark:text-amber-400">
                    <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
                    <p>{{ __('No leads yet') }}</p>
                    <p class="text-sm mt-1">{{ __('Enable pre-chat form to capture leads') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
