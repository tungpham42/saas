@extends('layouts.app')

@section('title', 'Welcome New Member - SaaS AI Chatbot')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8 animate-gentle">
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 dark:text-amber-400 mb-4 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Back to Community</span>
        </a>
        <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">Welcome a New Member 🎉</h1>
        <p class="text-amber-600 dark:text-amber-400 mt-2">Add someone special to our growing family</p>
    </div>

    <!-- Form Card -->
    <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.1s">
        <div class="gradient-warm px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-gift text-amber-900 text-xl"></i>
                <h2 class="text-amber-900 font-bold text-lg">Member Details</h2>
            </div>
            <p class="text-amber-800/70 text-sm mt-1">Let's get to know them</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-user mr-2 text-amber-500"></i>Full Name *
                    </label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           class="input-warm w-full"
                           placeholder="e.g., Sarah Johnson">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-envelope mr-2 text-amber-500"></i>Email Address *
                    </label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="input-warm w-full"
                           placeholder="sarah@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-lock mr-2 text-amber-500"></i>Password *
                    </label>
                    <input type="password" name="password" required
                           class="input-warm w-full"
                           placeholder="Create a secure password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-lock mr-2 text-amber-500"></i>Confirm Password *
                    </label>
                    <input type="password" name="password_confirmation" required
                           class="input-warm w-full"
                           placeholder="Type it again">
                </div>
            </div>

            <div class="border-t border-amber-100 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4 flex items-center gap-2">
                    <i class="fas fa-gift text-amber-500"></i>
                    <span>Plan Settings</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-robot mr-2 text-amber-500"></i>Bot Limit
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number" name="bot_limit" id="bot_limit" value="{{ old('bot_limit', 1) }}"
                                   min="1"
                                   class="input-warm w-32 text-center">
                            <div class="flex-1">
                                <div class="text-sm text-amber-500">
                                    <span class="font-semibold">Monthly Love: </span>
                                    <span id="revenue_display" class="text-green-600 font-bold">$0.00</span>
                                </div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-amber-500">Base plan includes 1 bot. Extra bots are $2/month each.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fab fa-paypal mr-2 text-amber-500"></i>PayPal Subscription ID
                        </label>
                        <input type="text" name="paypal_sub_id" value="{{ old('paypal_sub_id') }}"
                               class="input-warm w-full"
                               placeholder="I-XXXXXXXXX">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-heart mr-2 text-amber-500"></i>Subscription Status
                        </label>
                        <select name="paypal_sub_status"
                                class="input-warm w-full">
                            <option value="None" {{ old('paypal_sub_status') == 'None' ? 'selected' : '' }}>None</option>
                            <option value="Active" {{ old('paypal_sub_status') == 'Active' ? 'selected' : '' }}>Active 💝</option>
                            <option value="Suspended" {{ old('paypal_sub_status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="Cancelled" {{ old('paypal_sub_status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Plan Preview -->
            <div class="bg-amber-50 dark:bg-gray-800 rounded-2xl p-5">
                <h4 class="font-semibold text-amber-800 dark:text-amber-200 mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-simple"></i>
                    <span>Plan Preview</span>
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-amber-500">Base Bots:</span>
                        <span class="font-semibold text-amber-800 dark:text-amber-200 ml-2">1</span>
                    </div>
                    <div>
                        <span class="text-amber-500">Extra Bots:</span>
                        <span id="additional_bots" class="font-semibold text-amber-800 dark:text-amber-200 ml-2">0</span>
                    </div>
                    <div>
                        <span class="text-amber-500">Total Bots:</span>
                        <span id="total_bots" class="font-semibold text-amber-800 dark:text-amber-200 ml-2">1</span>
                    </div>
                    <div>
                        <span class="text-amber-500">Monthly:</span>
                        <span id="monthly_revenue" class="font-semibold text-green-600 ml-2">$0.00</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('users.index') }}" class="btn-outline-soft px-6 py-3">
                    Cancel
                </a>
                <button type="submit" class="btn-soft inline-flex items-center gap-2">
                    <i class="fas fa-heart"></i>
                    <span>Welcome Member</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Warm Tip -->
    <div class="mt-6 bg-amber-50 dark:bg-amber-900/20 rounded-2xl p-5 animate-gentle" style="animation-delay: 0.2s">
        <div class="flex items-start gap-3">
            <div class="bg-amber-100 dark:bg-amber-800 rounded-full p-2">
                <i class="fas fa-heart text-amber-600 dark:text-amber-400"></i>
            </div>
            <div>
                <h4 class="font-semibold text-amber-800 dark:text-amber-200">💝 Spread Kindness</h4>
                <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">
                    Every new member brings joy to our community. Welcome them warmly!
                </p>
            </div>
        </div>
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

        revenueDisplay.textContent = `$${revenue.toFixed(2)}/mo`;
        additionalBotsSpan.textContent = additional;
        totalBotsSpan.textContent = limit;
        monthlyRevenueSpan.textContent = `$${revenue.toFixed(2)}`;
    }

    botLimitInput.addEventListener('input', updateRevenue);
    updateRevenue();
</script>
@endpush
@endsection
