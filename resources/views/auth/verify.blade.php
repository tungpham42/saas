@extends('layouts.app')

@section('title', 'Verify Your Email - SaaS AI Chatbot')

@section('content')
<div class="max-w-md mx-auto">
    <div class="card-warm overflow-hidden animate-gentle">
        <div class="gradient-warm px-6 py-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4">
                <i class="fas fa-envelope text-amber-900 text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-amber-900">Check Your Inbox! 📧</h2>
            <p class="text-amber-800/80 mt-2">We've sent a magical link to your email</p>
        </div>

        <div class="p-8 text-center">
            <div class="bg-amber-50 dark:bg-gray-800 rounded-xl p-4 mb-6">
                <p class="text-amber-800 dark:text-amber-300 text-sm">
                    <i class="fas fa-envelope mr-2 text-amber-500"></i>
                    {{ auth()->user()->email }}
                </p>
            </div>

            <p class="text-amber-600 dark:text-amber-400 mb-6">
                Please click the verification link in your email to activate your account and start building amazing chatbots!
            </p>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl text-sm">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl text-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="space-y-3">
                <form action="{{ route('verification.resend') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-soft w-full inline-flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Send Again</span>
                    </button>
                </form>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-outline-soft w-full inline-flex items-center justify-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-6 border-t border-amber-100 dark:border-gray-700">
                <p class="text-xs text-amber-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Didn't receive the email? Check your spam folder or click "Send Again" above.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
