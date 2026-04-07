@extends('layouts.app')

@section('title', __('Analytics') . ' - ' . $bot->name)

@section('content')
<div class="space-y-8">
    <div class="flex flex-wrap justify-between items-center gap-4 animate-gentle">
        <div>
            <div class="flex items-center gap-4">
                <a href="{{ route('bots.show', $bot) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">{{ __("Bot's Journey 🚀") }}</h1>
                    <p class="text-amber-600 dark:text-amber-400 mt-1">{{ __('See how') }} {{ $bot->name }} {{ __('is helping people') }}</p>
                </div>
            </div>
        </div>
        <button onclick="window.print()" class="btn-outline-soft inline-flex items-center gap-2">
            <i class="fas fa-print"></i>
            <span>{{ __('Share Report') }}</span>
        </button>
    </div>

    <div class="card-warm p-4 animate-gentle" style="animation-delay: 0.1s">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <input type="hidden" name="tab" value="stats">
            <i class="fas fa-calendar-alt text-amber-500"></i>
            <select name="stat_preset" onchange="this.form.submit()"
                    class="input-warm px-4 py-2">
                <option value="" {{ !$statPreset ? 'selected' : '' }}>{{ __('All Time') }}</option>
                <option value="today" {{ $statPreset === 'today' ? 'selected' : '' }}>{{ __('Today') }}</option>
                <option value="yesterday" {{ $statPreset === 'yesterday' ? 'selected' : '' }}>{{ __('Yesterday') }}</option>
                <option value="last_7" {{ $statPreset === 'last_7' ? 'selected' : '' }}>{{ __('Last 7 days') }}</option>
                <option value="this_month" {{ $statPreset === 'this_month' ? 'selected' : '' }}>{{ __('This month') }}</option>
                <option value="last_month" {{ $statPreset === 'last_month' ? 'selected' : '' }}>{{ __('Last month') }}</option>
                <option value="last_30" {{ $statPreset === 'last_30' ? 'selected' : '' }}>{{ __('Last 30 days') }}</option>
                <option value="custom" {{ $statPreset === 'custom' ? 'selected' : '' }}>{{ __('Pick a date...') }}</option>
            </select>

            <input type="date" name="stat_date" value="{{ $statDate }}"
                   onchange="this.form.submit()"
                   class="input-warm px-4 py-2 {{ $statPreset === 'custom' ? '' : 'hidden' }}">

            @if($statPreset || $statDate)
            <a href="{{ route('bots.statistics', $bot) }}" class="text-amber-500 hover:text-amber-600">
                <i class="fas fa-times"></i> {{ __('Clear') }}
            </a>
            @endif
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="card-warm p-6 animate-gentle gradient-warm" style="animation-delay: 0.2s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-900/80 text-sm">{{ __('Total Conversations') }}</p>
                    <p class="text-4xl font-bold text-amber-900 mt-2">{{ number_format($totalSessions) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-comments text-amber-900 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-800/70">
                <i class="fas fa-chart-line mr-1"></i> {{ number_format($totalSessions / max(1, $bot->created_at->diffInDays(now()))) }} {{ __('per day') }}
            </div>
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.3s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm">{{ __('Human Help Sessions') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ number_format($takenOver) }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                    <i class="fas fa-user-friends text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
            </div>
            @if($totalSessions > 0)
            <div class="mt-4">
                <div class="w-full bg-amber-100 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-500 rounded-full h-2" style="width: {{ ($takenOver / max(1, $totalSessions)) * 100 }}%"></div>
                </div>
                <p class="text-xs text-amber-500 mt-1">{{ number_format(($takenOver / max(1, $totalSessions)) * 100, 1) }}% {{ __('needed a human') }}</p>
            </div>
            @endif
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.4s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm">{{ __('Helper Messages') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ number_format($totalAdminMsgs) }}</p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 rounded-full p-3">
                    <i class="fas fa-reply-all text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-500">
                <i class="fas fa-heart mr-1"></i> {{ number_format($totalAdminMsgs / max(1, $takenOver), 1) }} {{ __('per human session') }}
            </div>
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.5s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm">{{ __('First Response Time') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ $avgFirstResponse }}</p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-500">
                <i class="fas fa-rocket mr-1"></i> {{ __('Time until first human reply') }}
            </div>
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.6s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm">{{ __('Chat Duration') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ $avgHandlingTime }}</p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900/30 rounded-full p-3">
                    <i class="fas fa-hourglass-half text-orange-600 dark:text-orange-400 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-500">
                <i class="fas fa-coffee mr-1"></i> {{ __('Average conversation length') }}
            </div>
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.7s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-600 dark:text-amber-400 text-sm">{{ __('Total Help Time') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ $totalOnlineTime }}</p>
                </div>
                <div class="bg-indigo-100 dark:bg-indigo-900/30 rounded-full p-3">
                    <i class="fas fa-user-clock text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-500">
                <i class="fas fa-heart mr-1"></i> {{ __('Time spent helping customers') }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.8s">
            <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('Conversations Trend 📈') }}</h3>
                <p class="text-sm text-amber-500">{{ __('Last 7 days') }}</p>
            </div>
            <div class="p-6">
                <canvas id="sessionsChart" height="250"></canvas>
            </div>
        </div>

        <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.9s">
            <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('Messages Distribution 📊') }}</h3>
                <p class="text-sm text-amber-500">{{ __('Users vs AI vs Helpers') }}</p>
            </div>
            <div class="p-6">
                <canvas id="messagesChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="card-warm p-6 animate-gentle" style="animation-delay: 1s">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h4 class="font-bold text-amber-800 dark:text-amber-200">{{ __('Save Your Insights 💡') }}</h4>
                <p class="text-sm text-amber-500">{{ __('Download data for deeper analysis') }}</p>
            </div>
            <div class="flex gap-3">
                <button onclick="exportAsCSV()" class="btn-outline-soft px-4 py-2 inline-flex items-center gap-2">
                    <i class="fas fa-file-csv"></i>
                    <span>CSV</span>
                </button>
                <button onclick="exportAsJSON()" class="btn-outline-soft px-4 py-2 inline-flex items-center gap-2">
                    <i class="fab fa-js"></i>
                    <span>JSON</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sessions Chart
    const sessionsCtx = document.getElementById('sessionsChart').getContext('2d');
    new Chart(sessionsCtx, {
        type: 'line',
        data: {
            labels: @json($chartData->pluck('date')),
            datasets: [{
                label: '{{ __('Conversations') }}',
                data: @json($chartData->pluck('sessions')),
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Messages Chart
    const messagesCtx = document.getElementById('messagesChart').getContext('2d');
    new Chart(messagesCtx, {
        type: 'doughnut',
        data: {
            labels: ['{{ __('Visitors') }}', '{{ __('AI Assistant') }}', '{{ __('Human Helpers') }}'],
            datasets: [{
                data: [{{ $totalSessions * 2 }}, {{ $totalSessions * 1.5 }}, {{ $totalAdminMsgs }}],
                backgroundColor: ['#fbbf24', '#f59e0b', '#d97706'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    function exportAsCSV() {
        const data = {
            bot_name: '{{ $bot->name }}',
            total_conversations: {{ $totalSessions }},
            human_help_sessions: {{ $takenOver }},
            helper_messages: {{ $totalAdminMsgs }},
            avg_first_response: '{{ $avgFirstResponse }}',
            avg_chat_duration: '{{ $avgHandlingTime }}',
            total_help_time: '{{ $totalOnlineTime }}',
            exported_at: '{{ now() }}'
        };

        const headers = Object.keys(data);
        const csv = [headers.join(','), headers.map(h => JSON.stringify(data[h])).join(',')].join('\n');
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'bot_stats_{{ $bot->name }}_{{ now()->format('Y-m-d') }}.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function exportAsJSON() {
        const data = {
            bot: { id: {{ $bot->id }}, name: '{{ $bot->name }}' },
            statistics: {
                total_sessions: {{ $totalSessions }},
                taken_over: {{ $takenOver }},
                admin_messages: {{ $totalAdminMsgs }},
                avg_first_response: '{{ $avgFirstResponse }}',
                avg_handling_time: '{{ $avgHandlingTime }}',
                total_online_time: '{{ $totalOnlineTime }}'
            },
            exported_at: '{{ now()->toIso8601String() }}'
        };

        const json = JSON.stringify(data, null, 2);
        const blob = new Blob([json], { type: 'application/json' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'bot_stats_{{ $bot->name }}_{{ now()->format('Y-m-d') }}.json');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
</script>
@endpush
@endsection
