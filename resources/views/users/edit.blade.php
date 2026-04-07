@extends('layouts.app')

@section('title', __('Care for ') . $user->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8 animate-gentle">
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 dark:text-amber-400 mb-4 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>{{ __('Back to Community') }}</span>
        </a>
        <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">{{ __('Care for ') }}{{ $user->name }} 🌟</h1>
        <p class="text-amber-600 dark:text-amber-400 mt-2">{{ __('Update their journey with us') }}</p>
    </div>

    <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.1s">
        <div class="gradient-warm px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 rounded-full w-12 h-12 flex items-center justify-center">
                    <i class="fas fa-user-edit text-amber-900 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-amber-900 font-bold text-lg">{{ $user->name }}</h2>
                    <p class="text-amber-800/70 text-sm">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-user mr-2 text-amber-500"></i>{{ __('Full Name') }}
                    </label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                           class="input-warm w-full">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-envelope mr-2 text-amber-500"></i>{{ __('Email Address') }}
                    </label>
                    <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                           class="input-warm w-full">
                </div>
            </div>

            <div class="border-t border-amber-100 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4 flex items-center gap-2">
                    <i class="fas fa-key text-amber-500"></i>
                    <span>{{ __('Update Password') }}</span>
                </h3>
                <p class="text-sm text-amber-500 mb-4">{{ __('Leave blank to keep current password') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            {{ __('New Password') }}
                        </label>
                        <input type="password" name="password"
                               class="input-warm w-full"
                               placeholder="{{ __('Create new password') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            {{ __('Confirm New Password') }}
                        </label>
                        <input type="password" name="password_confirmation"
                               class="input-warm w-full"
                               placeholder="{{ __('Type it again') }}">
                    </div>
                </div>
            </div>

            <div class="border-t border-amber-100 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4 flex items-center gap-2">
                    <i class="fas fa-gift text-amber-500"></i>
                    <span>{{ __('Plan & Subscription') }}</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-robot mr-2 text-amber-500"></i>{{ __('Bot Limit') }}
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number" name="bot_limit" id="bot_limit" value="{{ old('bot_limit', $user->bot_limit) }}"
                                   min="1"
                                   class="input-warm w-32 text-center">
                            <div class="flex-1">
                                <div class="text-sm text-amber-500">
                                    <span class="font-semibold">{{ __('Monthly Love: ') }}</span>
                                    <span id="revenue_display" class="text-green-600 font-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fab fa-paypal mr-2 text-amber-500"></i>{{ __('PayPal Subscription ID') }}
                        </label>
                        <input type="text" name="paypal_sub_id" value="{{ old('paypal_sub_id', $user->paypal_sub_id) }}"
                               class="input-warm w-full"
                               placeholder="I-XXXXXXXXX">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-heart mr-2 text-amber-500"></i>{{ __('Subscription Status') }}
                        </label>
                        <select name="paypal_sub_status"
                                class="input-warm w-full">
                            <option value="None" {{ $user->paypal_sub_status == 'None' ? 'selected' : '' }}>{{ __('None') }}</option>
                            <option value="Active" {{ $user->paypal_sub_status == 'Active' ? 'selected' : '' }}>{{ __('Active 💝') }}</option>
                            <option value="Suspended" {{ $user->paypal_sub_status == 'Suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                            <option value="Cancelled" {{ $user->paypal_sub_status == 'Cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 dark:bg-gray-800 rounded-xl p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-xs text-amber-500">{{ __('Member Since') }}</p>
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-amber-500">{{ __('Bot Family') }}</p>
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">{{ $user->bots()->count() }} bots</p>
                    </div>
                    <div>
                        <p class="text-xs text-amber-500">{{ __('Email Status') }}</p>
                        <p class="text-sm font-semibold">
                            @if($user->hasVerifiedEmail())
                                <span class="text-green-600"><i class="fas fa-check-circle"></i> {{ __('Verified') }}</span>
                            @else
                                <span class="text-yellow-600"><i class="fas fa-envelope"></i> {{ __('Pending') }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-amber-500">{{ __('Member ID') }}</p>
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">#{{ $user->id }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('users.index') }}" class="btn-outline-soft px-6 py-3">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn-soft inline-flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>{{ __('Update Member') }}</span>
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
