@extends('layouts.app')

@section('title', __('Admin Dashboard - SaaS AI Chatbot'))

@section('content')
<div class="space-y-8">
    <div class="gradient-warm rounded-2xl p-8 text-amber-900 shadow-xl animate-gentle relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ __('Welcome, Admin! 👋') }}</h1>
            <p class="text-amber-800 text-lg">{{ __('You\'re making the world a kinder place, one chat at a time.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.1s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-500 text-sm">{{ __('Community Members') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="gradient-warm rounded-full p-3">
                    <i class="fas fa-users text-amber-900 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-500">
                <i class="fas fa-heart mr-1"></i> {{ __('Growing family') }}
            </div>
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.2s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-500 text-sm">{{ __('Bot Family') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ number_format($totalBots) }}</p>
                </div>
                <div class="gradient-warm rounded-full p-3">
                    <i class="fas fa-robot text-amber-900 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-500">
                <i class="fas fa-smile mr-1"></i> {{ __('Helping hands') }}
            </div>
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.3s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-500 text-sm">{{ __('Messages Shared') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">{{ number_format($totalMessages) }}</p>
                </div>
                <div class="gradient-warm rounded-full p-3">
                    <i class="fas fa-comments text-amber-900 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-500">
                <i class="fas fa-heart mr-1"></i> {{ __('Conversations') }}
            </div>
        </div>

        <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.4s">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-500 text-sm">{{ __('Monthly Love') }}</p>
                    <p class="text-3xl font-bold text-amber-800 dark:text-amber-200 mt-2">${{ number_format($totalMonthlyRevenue, 2) }}</p>
                </div>
                <div class="gradient-warm rounded-full p-3">
                    <i class="fas fa-dollar-sign text-amber-900 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-amber-500">
                <i class="fas fa-chart-line mr-1"></i> {{ __('Supporting kindness') }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.5s">
            <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('Messages Over Time 📈') }}</h3>
                <p class="text-sm text-amber-500">{{ __('Last 30 days of kindness') }}</p>
            </div>
            <div class="p-6">
                <canvas id="messagesChart" height="300"></canvas>
            </div>
        </div>

        <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.6s">
            <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('Community Growth 🌿') }}</h3>
                <p class="text-sm text-amber-500">{{ __('New members joining the family') }}</p>
            </div>
            <div class="p-6">
                <canvas id="usersChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.7s">
            <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('New Family Members 👥') }}</h3>
                <a href="{{ route('users.index') }}" class="text-sm text-amber-500 hover:text-amber-600">{{ __('View All') }} <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="divide-y divide-amber-100 dark:divide-gray-700">
                @forelse($recentUsers as $user)
                <div class="p-4 hover:bg-amber-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="gradient-warm rounded-full w-10 h-10 flex items-center justify-center">
                                <span class="text-amber-900 font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-amber-800 dark:text-amber-200">{{ $user->name }}</p>
                                <p class="text-sm text-amber-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-amber-400">{{ $user->created_at->diffForHumans() }}</span>
                            <p class="text-xs text-amber-500 mt-1">{{ $user->bots_count ?? 0 }} {{ __('bots') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-amber-400">
                    <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
                    <p>{{ __('No new members yet') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.8s">
            <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('New Bot Friends 🤖') }}</h3>
                <a href="{{ route('bots.index') }}" class="text-sm text-amber-500 hover:text-amber-600">{{ __('View All') }} <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="divide-y divide-amber-100 dark:divide-gray-700">
                @forelse($recentBots as $bot)
                <div class="p-4 hover:bg-amber-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-amber-100 dark:bg-amber-900/30 rounded-full w-10 h-10 flex items-center justify-center">
                                <i class="fas fa-robot text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-amber-800 dark:text-amber-200">{{ $bot->name }}</p>
                                <p class="text-sm text-amber-500">{{ __('Parent:') }} {{ $bot->user->name ?? __('Unknown') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-amber-400">{{ $bot->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-amber-400">
                    <i class="fas fa-robot text-4xl mb-3 opacity-50"></i>
                    <p>{{ __('No new bots yet') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.9s">
        <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200">{{ __('Revenue Love 💖') }}</h3>
            <p class="text-sm text-amber-500">{{ __('Supporting kindness through subscriptions') }}</p>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-amber-600">{{ __('Free Plans (Spreaders of Joy)') }}</span>
                        <span class="font-semibold text-amber-800">$0</span>
                    </div>
                    <div class="w-full bg-amber-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-amber-300 rounded-full h-2" style="width: 0%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-amber-600">{{ __('Paid Plans (Super Supporters)') }}</span>
                        <span class="font-semibold text-amber-800">${{ number_format($totalMonthlyRevenue, 2) }}</span>
                    </div>
                    <div class="w-full bg-amber-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-amber-500 rounded-full h-2" style="width: {{ min(100, ($totalMonthlyRevenue / 1000) * 100) }}%"></div>
                    </div>
                </div>
            </div>
            <div class="mt-6 p-4 bg-amber-50 dark:bg-gray-800 rounded-xl text-center">
                <p class="text-sm text-amber-600">
                    <i class="fas fa-heart text-red-500"></i>
                    {{ __('Thank you for making kindness sustainable!') }}
                </p>
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
                label: '{{ __('Messages') }}',
                data: @json($chartData->pluck('messages')),
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#f59e0b',
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
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(245, 158, 11, 0.1)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Users Chart
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    new Chart(usersCtx, {
        type: 'bar',
        data: {
            labels: @json($chartData->pluck('date')),
            datasets: [{
                label: '{{ __('New Members') }}',
                data: @json($chartData->pluck('users')),
                backgroundColor: 'rgba(245, 158, 11, 0.8)',
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
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(245, 158, 11, 0.1)' }
                }
            }
        }
    });
</script>
@endpush
@endsection
