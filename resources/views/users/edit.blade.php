@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-4 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Back to Users</span>
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit User</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Update user information and plan settings</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="gradient-primary px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-user-edit text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-lg">{{ $user->name }}</h2>
                    <p class="text-white/70 text-sm">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user mr-2 text-blue-500"></i>Full Name *
                    </label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i>Email Address *
                    </label>
                    <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-key text-yellow-500"></i>
                    <span>Change Password (Optional)</span>
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Leave blank to keep current password</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            New Password
                        </label>
                        <input type="password" name="password"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition"
                               placeholder="Leave blank to keep current">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition"
                               placeholder="Confirm new password">
                    </div>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                            <input type="number" name="bot_limit" id="bot_limit" value="{{ old('bot_limit', $user->bot_limit) }}"
                                   min="1"
                                   class="w-32 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-center">
                            <div class="flex-1">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold">Monthly Revenue: </span>
                                    <span id="revenue_display" class="text-green-600 font-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fab fa-paypal mr-2 text-blue-500"></i>PayPal Subscription ID
                        </label>
                        <input type="text" name="paypal_sub_id" value="{{ old('paypal_sub_id', $user->paypal_sub_id) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="I-XXXXXXXXX">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tag mr-2 text-blue-500"></i>Subscription Status
                        </label>
                        <select name="paypal_sub_status"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="None" {{ $user->paypal_sub_status == 'None' ? 'selected' : '' }}>None</option>
                            <option value="Active" {{ $user->paypal_sub_status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Suspended" {{ $user->paypal_sub_status == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="Cancelled" {{ $user->paypal_sub_status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- User Stats Card -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">User ID</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">#{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Member Since</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Current Bots</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->bots()->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email Status</p>
                        <p class="text-sm font-semibold {{ $user->hasVerifiedEmail() ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $user->hasVerifiedEmail() ? 'Verified' : 'Unverified' }}
                        </p>
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
                    <span>Update User</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const botLimitInput = document.getElementById('bot_limit');
    const revenueDisplay = document.getElementById('revenue_display');

    function updateRevenue() {
        let limit = parseInt(botLimitInput.value) || 1;
        let additional = Math.max(0, limit - 1);
        let revenue = additional * 2;
        revenueDisplay.textContent = `$${revenue.toFixed(2)}/mo`;
    }

    botLimitInput.addEventListener('input', updateRevenue);
    updateRevenue();
</script>
@endpush
@endsection
