@extends('layouts.app')

@section('title', 'Create User - SaaS AI Chatbot')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-4 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Back to Users</span>
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add New User</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Create a new user account for the platform</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="gradient-primary px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-user-plus text-white text-xl"></i>
                <h2 class="text-white font-bold text-lg">User Information</h2>
            </div>
            <p class="text-white/70 text-sm mt-1">Enter the user's details and plan settings</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user mr-2 text-blue-500"></i>Full Name *
                    </label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition"
                           placeholder="John Doe">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i>Email Address *
                    </label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition"
                           placeholder="user@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-lock mr-2 text-blue-500"></i>Password *
                    </label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-lock mr-2 text-blue-500"></i>Confirm Password *
                    </label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-green-500"></i>
                    <span>Plan & Subscription Settings</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-robot mr-2 text-blue-500"></i>Bot Limit
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number" name="bot_limit" id="bot_limit" value="{{ old('bot_limit', 1) }}"
                                   min="1"
                                   class="w-32 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-center">
                            <div class="flex-1">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold">Monthly Revenue: </span>
                                    <span id="revenue_display" class="text-green-600 font-bold">$0.00</span>
                                </div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Base plan includes 1 bot. Additional bots cost $2/month each.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fab fa-paypal mr-2 text-blue-500"></i>PayPal Subscription ID
                        </label>
                        <input type="text" name="paypal_sub_id" value="{{ old('paypal_sub_id') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="I-XXXXXXXXX">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tag mr-2 text-blue-500"></i>Subscription Status
                        </label>
                        <select name="paypal_sub_status"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="None" {{ old('paypal_sub_status') == 'None' ? 'selected' : '' }}>None</option>
                            <option value="Active" {{ old('paypal_sub_status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Suspended" {{ old('paypal_sub_status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="Cancelled" {{ old('paypal_sub_status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Plan Preview Card -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl p-5">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-simple"></i>
                    <span>Plan Preview</span>
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Base Bots:</span>
                        <span class="font-semibold text-gray-900 dark:text-white ml-2">1</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Additional Bots:</span>
                        <span id="additional_bots" class="font-semibold text-gray-900 dark:text-white ml-2">0</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Total Bots:</span>
                        <span id="total_bots" class="font-semibold text-gray-900 dark:text-white ml-2">1</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Monthly Revenue:</span>
                        <span id="monthly_revenue" class="font-semibold text-green-600 dark:text-green-400 ml-2">$0.00</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('users.index') }}"
                   class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                    Cancel
                </a>
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Create User</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const botLimitInput = document.getElementById('bot_limit');
    const revenueDisplay = document.getElementById('revenue_display');
    const additionalBotsSpan = document.getElementById('additional_bots');
    const totalBotsSpan = document.getElementById('total_bots');
    const monthlyRevenueSpan = document.getElementById('monthly_revenue');

    function updateRevenue() {
        let limit = parseInt(botLimitInput.value) || 1;
        let additional = Math.max(0, limit - 1);
        let revenue = additional * 2;

        revenueDisplay.textContent = `$${revenue.toFixed(2)}`;
        additionalBotsSpan.textContent = additional;
        totalBotsSpan.textContent = limit;
        monthlyRevenueSpan.textContent = `$${revenue.toFixed(2)}`;
    }

    botLimitInput.addEventListener('input', updateRevenue);
    updateRevenue();
</script>
@endpush
@endsection
