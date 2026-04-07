@extends('layouts.app')

@section('title', __('Create New Chatbot') . ' - SaaS AI Chatbot')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('bots.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-4 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>{{ __('Back to Bots') }}</span>
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Create New Chatbot') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Launch your AI assistant in minutes') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="gradient-primary px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-robot text-white text-xl"></i>
                <h2 class="text-white font-bold text-lg">{{ __('Bot Configuration') }}</h2>
            </div>
            <p class="text-white/70 text-sm mt-1">{{ __('Give your bot a name to get started') }}</p>
        </div>

        <form action="{{ route('bots.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-tag mr-2 text-blue-500"></i>{{ __('Bot Name') }}
                </label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name') }}"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition"
                       placeholder="{{ __('e.g., Customer Support Bot, Sales Assistant, FAQ Bot') }}">
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>{{ __('Choose a descriptive name to easily identify your bot') }}
                </p>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl p-5">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-rocket text-blue-500"></i>
                    <span>{{ __('What\'s Next?') }}</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>{{ __('Configure AI model & prompts') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>{{ __('Connect social media channels') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>{{ __('Upload knowledge base documents') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>{{ __('Customize chat widget appearance') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>{{ __('Get embed code for your website') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>{{ __('Monitor live chats & analytics') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('bots.index') }}"
                   class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>{{ __('Create Bot') }}</span>
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-lg animate-fade-in-up" style="animation-delay: 0.2s">
        <div class="flex items-start gap-3">
            <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-2">
                <i class="fas fa-lightbulb text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white">{{ __('Pro Tip') }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Give your bot a name that reflects its purpose. For example, "Sales Assistant" or "Customer Support Bot". You can always change the name later in settings.') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
