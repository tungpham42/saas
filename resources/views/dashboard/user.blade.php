@extends('layouts.app')

@section('title', 'Dashboard - SaaS AI Chatbot')

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="gradient-primary rounded-2xl p-8 text-white shadow-xl animate-fade-in-up">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Welcome back, {{ $user->name }}! 👋</h1>
                <p class="text-white/80 text-lg">Ready to help more customers today?</p>
            </div>
            @if($remainingSlots > 0)
            <a href="{{ route('bots.create') }}"
               class="bg-white/20 backdrop-blur-sm px-6 py-3 rounded-xl font-semibold hover:bg-white/30 transition-all flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Create New Bot</span>
            </a>
            @endif
        </div>

        <!-- Stats Mini -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">
            <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                <p class="text-white/70 text-sm">Bot Limit</p>
                <p class="text-2xl font-bold">{{ $botCount }} / {{ $botLimit }}</p>
                <div class="w-full bg-white/20 rounded-full h-2 mt-2">
                    <div class="bg-white rounded-full h-2" style="width: {{ ($botCount / max(1, $botLimit)) * 100 }}%"></div>
                </div>
            </div>
            <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                <p class="text-white/70 text-sm">Available Slots</p>
                <p class="text-2xl font-bold">{{ $remainingSlots }}</p>
                <p class="text-xs text-white/60 mt-1">{{ $remainingSlots > 0 ? 'Ready to create' : 'Upgrade to create more' }}</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                <p class="text-white/70 text-sm">Active Bots</p>
                <p class="text-2xl font-bold">{{ $botCount }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Update colors for better contrast -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all card-hover animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Messages</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($totalMessages) }}</p>
                </div>
                <div class="gradient-primary rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                    <i class="fas fa-comments text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-chart-line mr-1"></i> Lifetime conversations
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all card-hover animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Leads</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($totalLeads) }}</p>
                </div>
                <div class="gradient-secondary rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-user-plus mr-1"></i> Captured from forms
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all card-hover animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Bot Usage</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $botCount }}</p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 rounded-full w-12 h-12 flex items-center justify-center">
                    <i class="fas fa-robot text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-chart-simple mr-1"></i> Active deployments
            </div>
        </div>
    </div>

    <!-- Bots Overview -->
    @if(count($botsUsage) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.4s">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Your Bots Overview</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Performance metrics for each bot</p>
            </div>
            <a href="{{ route('bots.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 text-sm font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bot Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Messages</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Leads</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sessions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($botsUsage as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="gradient-primary rounded-lg w-8 h-8 flex items-center justify-center">
                                    <i class="fas fa-robot text-white text-xs"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['bot']->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Created {{ $item['bot']->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900 dark:text-white font-semibold">{{ number_format($item['messages']) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900 dark:text-white font-semibold">{{ number_format($item['leads']) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900 dark:text-white font-semibold">{{ number_format($item['sessions']) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('bots.show', $item['bot']) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium">
                                Manage <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Sessions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Chat Sessions</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Latest conversations</p>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                @forelse($recentSessions as $session)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-comment-dots text-blue-500 text-sm"></i>
                                <code class="text-xs font-mono text-gray-600 dark:text-gray-400">{{ substr($session->session_id, 0, 25) }}...</code>
                            </div>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span><i class="far fa-calendar-alt mr-1"></i> {{ $session->created_at->format('M d, Y H:i') }}</span>
                                <span><i class="fas fa-reply-all mr-1"></i> {{ $session->admin_msg_count }} admin replies</span>
                            </div>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $session->admin_msg_count > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $session->admin_msg_count > 0 ? 'Human helped' : 'AI only' }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-comments text-4xl mb-3 opacity-50"></i>
                    <p>No recent sessions</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Leads -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Leads</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">New customer inquiries</p>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                @forelse($recentLeads as $lead)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-circle text-green-500 text-lg"></i>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $lead->customer_name }}</span>
                            </div>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span><i class="fas fa-phone-alt mr-1"></i> {{ $lead->customer_phone }}</span>
                                <span><i class="far fa-clock mr-1"></i> {{ $lead->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('bots.history', $lead->bot_id) }}?session_id={{ urlencode($lead->session_id) }}"
                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-sm font-medium">
                            View <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
                    <p>No leads yet</p>
                    <p class="text-sm mt-1">Enable pre-chat form to capture leads</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
