@extends('layouts.app')

@section('title', 'My Profile - SaaS AI Chatbot')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="animate-fade-in-up">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Profile</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your account settings</p>
    </div>

    <!-- Profile Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="gradient-primary px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 rounded-full w-12 h-12 flex items-center justify-center">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-lg">{{ $user->name }}</h2>
                    <p class="text-white/70 text-sm">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-user-circle mr-2 text-blue-500"></i>Full Name
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-envelope mr-2 text-blue-500"></i>Email Address
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-lock text-yellow-500"></i>
                    <span>Change Password</span>
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Leave blank to keep current password</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                        <input type="password" name="current_password"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Update Profile</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">Account Statistics</h3>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Member Since</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email Status</p>
                <p class="font-semibold">
                    @if($user->hasVerifiedEmail())
                        <span class="text-green-600 dark:text-green-400"><i class="fas fa-check-circle"></i> Verified</span>
                    @else
                        <span class="text-yellow-600 dark:text-yellow-400"><i class="fas fa-exclamation-circle"></i> Not Verified</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Bots</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->bots()->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Bot Limit</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->bot_limit }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
