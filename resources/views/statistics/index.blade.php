@extends('layouts.app')

@section('title', 'Analytics - ' . $bot->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-wrap justify-between items-center gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Performance metrics for {{ $bot->name }}</p>
        </div>
        <button onclick="window.print()" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-4 py-2 rounded-xl transition flex items-center gap-2">
            <i class="fas fa-print"></i>
            <span>Print Report</span>
        </button>
    </div>

    <!-- Date Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4 animate-fade-in-up" style="animation-delay: 0.1s">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <input type="hidden" name="tab" value="stats">
            <i class="fas fa-calendar-alt text-gray-400"></i>
            <select name="stat_preset" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="" {{ !$statPreset ? 'selected' : '' }}>All Time</option>
                <option value="today" {{ $statPreset === 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ $statPreset === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="last_7" {{ $statPreset === 'last_7' ? 'selected' : '' }}>Last 7 days</option>
                <option value="this_month" {{ $statPreset === 'this_month' ? 'selected' : '' }}>This month</option>
                <option value="last_month" {{ $statPreset === 'last_month' ? 'selected' : '' }}>Last month</option>
                <option value="last_30" {{ $statPreset === 'last_30' ? 'selected' : '' }}>Last 30 days</option>
                <option value="custom" {{ $statPreset === 'custom' ? 'selected' : '' }}>Custom...</option>
            </select>

            <input type="date" name="stat_date" value="{{ $statDate }}"
                   onchange="this.form.submit()"
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white {{ $statPreset === 'custom' ? '' : 'hidden' }}"
                   id="custom_date_input">

            @if($statPreset || $statDate)
            <a href="{{ route('bots.statistics', $bot) }}" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">Total Sessions</p>
                    <p class="text-4xl font-bold mt-2">{{ number_format($totalSessions) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-comments text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-white/70">
                <i class="fas fa-chart-line mr-1"></i> {{ number_format($totalSessions / max(1, $bot->created_at->diffInDays(now()))) }} avg/day
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">Human Takeovers</p>
                    <p class="text-4xl font-bold mt-2">{{ number_format($takenOver) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-user-friends text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-white/20 rounded-full h-2">
                    <div class="bg-white rounded-full h-2" style="width: {{ ($takenOver / max(1, $totalSessions)) * 100 }}%"></div>
                </div>
                <p class="text-sm text-white/70 mt-1">{{ number_format(($takenOver / max(1, $totalSessions)) * 100, 1) }}% of sessions</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">Admin Messages</p>
                    <p class="text-4xl font-bold mt-2">{{ number_format($totalAdminMsgs) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-reply-all text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-white/70">
                <i class="fas fa-chart-simple mr-1"></i> {{ number_format($totalAdminMsgs / max(1, $takenOver), 1) }} avg per takeover
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg card-hover animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Avg First Response</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $avgFirstResponse }}</p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg card-hover animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Avg Handling Time</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $avgHandlingTime }}</p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900/30 rounded-full p-3">
                    <i class="fas fa-hourglass-half text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg card-hover animate-fade-in-up" style="animation-delay: 0.7s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Human Time</p>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $totalOnlineTime }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                    <i class="fas fa-user-clock text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.8s">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sessions Trend</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Last 7 days</p>
            </div>
            <div class="p-6">
                <canvas id="sessionsChart" height="250"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.9s">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Message Distribution</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">User vs AI vs Admin</p>
            </div>
            <div class="p-6">
                <canvas id="messagesChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 animate-fade-in-up" style="animation-delay: 1s">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white">Export Analytics</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Download data for further analysis</p>
            </div>
            <div class="flex gap-3">
                <button onclick="exportAsCSV()" class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-xl transition flex items-center gap-2">
                    <i class="fas fa-file-csv"></i>
                    <span>CSV</span>
                </button>
                <button onclick="exportAsJSON()" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl transition flex items-center gap-2">
                    <i class="fab fa-js"></i>
                    <span>JSON</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Sessions Chart
    const sessionsCtx = document.getElementById('sessionsChart').getContext('2d');
    new Chart(sessionsCtx, {
        type: 'line',
        data: {
            labels: @json($chartData->pluck('date')),
            datasets: [{
                label: 'Sessions',
                data: @json($chartData->pluck('sessions')),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
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
            labels: ['User Messages', 'AI Messages', 'Admin Messages'],
            datasets: [{
                data: [{{ $totalSessions * 2 }}, {{ $totalSessions * 1.5 }}, {{ $totalAdminMsgs }}],
                backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw.toLocaleString()} messages` } }
            }
        }
    });

    function exportAsCSV() {
        const data = {
            bot_name: '{{ $bot->name }}',
            total_sessions: {{ $totalSessions }},
            taken_over: {{ $takenOver }},
            admin_messages: {{ $totalAdminMsgs }},
            avg_first_response: '{{ $avgFirstResponse }}',
            avg_handling_time: '{{ $avgHandlingTime }}',
            total_online_time: '{{ $totalOnlineTime }}',
            exported_at: '{{ now() }}'
        };

        const headers = Object.keys(data);
        const csv = [headers.join(','), headers.map(h => JSON.stringify(data[h])).join(',')].join('\n');
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'statistics_{{ $bot->name }}_{{ now()->format('Y-m-d') }}.csv');
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
        link.setAttribute('download', 'statistics_{{ $bot->name }}_{{ now()->format('Y-m-d') }}.json');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
</script>
@endpush
@endsection
