@extends('layouts.app')

@section('title', 'Verify Email - SaaS AI Chatbot')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden animate-fade-in-up">
        <div class="gradient-primary px-6 py-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4">
                <i class="fas fa-envelope text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">Verify Your Email</h2>
            <p class="text-white/80 mt-2">We've sent a verification link to your email</p>
        </div>

        <div class="p-8 text-center">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 mb-6">
                <p class="text-blue-800 dark:text-blue-300 text-sm">
                    <i class="fas fa-envelope mr-2"></i>
                    {{ auth()->user()->email }}
                </p>
            </div>

            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Please check your inbox and click the verification link to activate your account.
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
                    <button type="submit"
                            class="w-full btn-primary py-3 rounded-xl text-white font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Resend Verification Email</span>
                    </button>
                </form>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center justify-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Didn't receive the email? Check your spam folder or click the button above to resend.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
