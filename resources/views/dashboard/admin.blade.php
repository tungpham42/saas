@extends('layouts.app')

@section('title', 'Admin Dashboard - SaaS AI Chatbot')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="animate-fade-in-up">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Platform overview and analytics</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">Total Users</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-white/70">
                <i class="fas fa-chart-line mr-1"></i> Active users
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">Total Bots</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalBots) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-robot text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-white/70">
                <i class="fas fa-chart-simple mr-1"></i> Active deployments
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">Total Messages</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalMessages) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-comments text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-white/70">
                <i class="fas fa-chart-line mr-1"></i> Lifetime conversations
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl p-6 text-white shadow-lg animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/80 text-sm">Monthly Revenue</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($totalMonthlyRevenue, 2) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-white/70">
                <i class="fas fa-chart-line mr-1"></i> MRR
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Messages Over Time</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Last 30 days</p>
            </div>
            <div class="p-6">
                <canvas id="messagesChart" height="300"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">User Growth</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">New users per day</p>
            </div>
            <div class="p-6">
                <canvas id="usersChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.7s">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Users</h3>
                <a href="{{ route('users.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View All <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentUsers as $user)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="gradient-primary rounded-full w-10 h-10 flex items-center justify-center">
                                <span class="text-white font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->bots_count ?? 0 }} bots</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
                    <p>No users yet</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Bots -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.8s">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Bots</h3>
                <a href="{{ route('bots.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View All <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentBots as $bot)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full w-10 h-10 flex items-center justify-center">
                                <i class="fas fa-robot text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $bot->name }}</p>
                                <p class="text-sm text-gray-500">Owner: {{ $bot->user->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-400">{{ $bot->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-robot text-4xl mb-3 opacity-50"></i>
                    <p>No bots yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Revenue Breakdown -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.9s">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Revenue Breakdown</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Monthly recurring revenue by plan</p>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Base Plans ($0)</span>
                        <span class="font-semibold text-gray-900 dark:text-white">$0</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-gray-400 rounded-full h-2" style="width: 0%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Add-on Bots ($2/bot)</span>
                        <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($totalMonthlyRevenue, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-500 rounded-full h-2" style="width: {{ min(100, ($totalMonthlyRevenue / 1000) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Messages Chart
    const messagesCtx = document.getElementById('messagesChart').getContext('2d');
    new Chart(messagesCtx, {
        type: 'line',
        data: {
            labels: @json($chartData->pluck('date')),
            datasets: [{
                label: 'Messages',
                data: @json($chartData->pluck('messages')),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#3b82f6',
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
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Users Chart (placeholder - you can add actual user growth data)
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    new Chart(usersCtx, {
        type: 'bar',
        data: {
            labels: @json($chartData->pluck('date')),
            datasets: [{
                label: 'New Users',
                data: @json($chartData->pluck('messages')->map(function($item) { return rand(0, 5); })),
                backgroundColor: 'rgba(139, 92, 246, 0.8)',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush
@endsection
