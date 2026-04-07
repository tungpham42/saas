@extends('layouts.app')

@section('title', __('Verify Your Email') . ' - SaaS AI Chatbot')

@section('content')
<div class="max-w-md mx-auto">
    <div class="card-warm overflow-hidden animate-gentle">
        <div class="gradient-warm px-6 py-8 text-center text-amber-900">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4">
                <i class="fas fa-envelope text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold">{{ __('Check Your Inbox! 📧') }}</h2>
            <p class="opacity-80 mt-2">{{ __('We\'ve sent a magical link to your email') }}</p>
        </div>

        <div class="p-8 text-center">
            <div class="bg-amber-50 dark:bg-gray-800 rounded-xl p-4 mb-6">
                <p class="text-amber-800 dark:text-amber-300 text-sm font-medium">
                    <i class="fas fa-envelope mr-2 text-amber-500"></i>{{ auth()->user()->email }}
                </p>
            </div>

            <p class="text-amber-600 dark:text-amber-400 mb-6 text-sm">
                {{ __('Please click the verification link in your email to activate your account and start building amazing chatbots!') }}
            </p>

            <div class="space-y-3">
                <form action="{{ route('verification.resend') }}" method="POST">
                    @csrf
                    <button type="submit" class="gradient-warm text-amber-900 font-bold w-full py-3 rounded-xl flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i><span>{{ __('Send Again') }}</span>
                    </button>
                </form>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-xl border-2 border-amber-200 text-amber-600 font-semibold flex items-center justify-center gap-2 hover:bg-amber-50 dark:hover:bg-gray-800 transition">
                        <i class="fas fa-sign-out-alt"></i><span>{{ __('Sign Out') }}</span>
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-6 border-t border-amber-100 dark:border-gray-700 flex flex-col items-center gap-4">
                <div class="relative" x-data="{ langOpen: false }">
                    <button @click="langOpen = !langOpen" @click.away="langOpen = false" class="px-3 py-1.5 rounded-full bg-amber-50 dark:bg-gray-800 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-gray-700 transition-all flex items-center gap-2 border border-amber-200 dark:border-gray-600">
                        <i class="fas fa-globe text-sm"></i>
                        <span class="uppercase text-xs font-bold">{{ app()->getLocale() }}</span>
                        <i class="fas fa-chevron-down text-xs ml-1"></i>
                    </button>
                    <div x-show="langOpen" x-transition class="absolute bottom-full mb-2 right-1/2 translate-x-1/2 w-32 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-amber-100 dark:border-gray-700 overflow-hidden text-left" style="display: none;">
                        <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="block px-4 py-2 text-sm text-amber-800 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-700">English</a>
                        <a href="{{ request()->fullUrlWithQuery(['lang' => 'vi']) }}" class="block px-4 py-2 text-sm text-amber-800 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-700">Tiếng Việt</a>
                    </div>
                </div>

                <p class="text-[10px] text-amber-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ __('Didn\'t receive the email? Check your spam folder or click "Send Again" above.') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
