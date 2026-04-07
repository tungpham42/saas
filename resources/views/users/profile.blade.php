@extends('layouts.app')

@section('title', __('My Profile - SaaS AI Chatbot'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-gentle">
        <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">{{ __('My Profile 🌟') }}</h1>
        <p class="text-amber-600 dark:text-amber-400 mt-1">{{ __('Your personal space in the community') }}</p>
    </div>

    <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.1s">
        <div class="gradient-warm px-6 py-4">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 rounded-full w-20 h-20 flex items-center justify-center">
                    <i class="fas fa-user-circle text-amber-900 text-5xl"></i>
                </div>
                <div>
                    <h2 class="text-amber-900 font-bold text-2xl">{{ $user->name }}</h2>
                    <p class="text-amber-800/70 text-sm">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if($user->hasVerifiedEmail())
                            <span class="text-xs bg-green-500/20 text-green-700 px-2 py-0.5 rounded-full">
                                <i class="fas fa-check-circle"></i> {{ __('Verified') }}
                            </span>
                        @else
                            <span class="text-xs bg-yellow-500/20 text-yellow-700 px-2 py-0.5 rounded-full">
                                <i class="fas fa-envelope"></i> {{ __('Not Verified') }}
                            </span>
                        @endif
                        <span class="text-xs bg-amber-500/20 text-amber-700 px-2 py-0.5 rounded-full">
                            <i class="fas fa-calendar"></i> {{ __('Member since ') }}{{ $user->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                    <i class="fas fa-user-circle mr-2 text-amber-500"></i>{{ __('Your Name') }}
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="input-warm w-full">
            </div>

            <div>
                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                    <i class="fas fa-envelope mr-2 text-amber-500"></i>{{ __('Email Address') }}
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="input-warm w-full">
                @if(!$user->hasVerifiedEmail())
                    <p class="mt-1 text-xs text-amber-500">
                        <i class="fas fa-info-circle"></i>
                        <a href="{{ route('verification.notice') }}" class="text-amber-600 hover:underline">{{ __('Verify your email') }}</a>{{ __(' to access all features.') }}
                    </p>
                @endif
            </div>

            <div class="border-t border-amber-100 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4 flex items-center gap-2">
                    <i class="fas fa-lock text-amber-500"></i>
                    <span>{{ __('Update Password') }}</span>
                </h3>
                <p class="text-sm text-amber-500 mb-4">{{ __('Leave blank to keep your current password') }}</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Current Password') }}</label>
                        <input type="password" name="current_password"
                               class="input-warm w-full"
                               placeholder="{{ __('Enter current password') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('New Password') }}</label>
                        <input type="password" name="password"
                               class="input-warm w-full"
                               placeholder="{{ __('Create new password') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Confirm New Password') }}</label>
                        <input type="password" name="password_confirmation"
                               class="input-warm w-full"
                               placeholder="{{ __('Confirm new password') }}">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-soft inline-flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>

    <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.2s">
        <div class="px-6 py-4 border-b border-amber-100 dark:border-gray-700">
            <h3 class="font-bold text-amber-800 dark:text-amber-200">{{ __('Your Journey 📊') }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-amber-50 dark:bg-gray-800 rounded-xl">
                    <p class="text-2xl font-bold text-amber-800 dark:text-amber-200">{{ $user->bots()->count() }}</p>
                    <p class="text-xs text-amber-500">{{ __('Bot Family') }}</p>
                </div>
                <div class="text-center p-3 bg-amber-50 dark:bg-gray-800 rounded-xl">
                    <p class="text-2xl font-bold text-amber-800 dark:text-amber-200">{{ $user->bot_limit }}</p>
                    <p class="text-xs text-amber-500">{{ __('Bot Limit') }}</p>
                </div>
                <div class="text-center p-3 bg-amber-50 dark:bg-gray-800 rounded-xl">
                    <p class="text-2xl font-bold text-amber-800 dark:text-amber-200">{{ $user->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-amber-500">{{ __('Joined') }}</p>
                </div>
                <div class="text-center p-3 bg-amber-50 dark:bg-gray-800 rounded-xl">
                    <p class="text-2xl font-bold text-amber-800 dark:text-amber-200">
                        ${{ max(0, $user->bot_limit - 1) * 2 }}
                    </p>
                    <p class="text-xs text-amber-500">{{ __('Monthly Love') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
