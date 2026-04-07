<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" x-init="initTheme()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Welcome Back') }} - SaaS AI Chatbot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #fef9e7 0%, #fff5e6 100%); }
        .dark body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); }
        .gradient-warm { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
        .card-warm { background: #ffffff; border-radius: 1.5rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .dark .card-warm { background: #1f2937; }
        .input-warm { background-color: #fffbeb; border: 2px solid #fde68a; border-radius: 1rem; padding: 0.75rem 1rem; width: 100%; }
        .dark .input-warm { background-color: #374151; border-color: #d97706; color: #fef3c7; }
        .btn-warm { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); font-weight: 600; padding: 0.75rem; border-radius: 1rem; width: 100%; transition: transform 0.2s; }
        .btn-warm:hover { transform: translateY(-2px); }
    </style>
</head>
<body :class="{ 'dark': isDarkMode }" class="min-h-screen flex items-center justify-center p-4 relative">

    <div class="absolute top-4 right-4 flex items-center gap-3 z-50">
        <div class="relative" x-data="{ langOpen: false }">
            <button @click="langOpen = !langOpen" @click.away="langOpen = false" class="px-3 py-2 rounded-full bg-amber-100 dark:bg-gray-800 text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-gray-700 transition-all flex items-center gap-2">
                <i class="fas fa-globe"></i>
                <span class="uppercase text-sm font-bold">{{ app()->getLocale() }}</span>
            </button>
            <div x-show="langOpen" x-transition class="absolute right-0 mt-2 w-32 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-amber-100 dark:border-gray-700 overflow-hidden" style="display: none;">
                <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-amber-800 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-700">🇺🇸 English</a>
                <a href="{{ route('language.switch', 'vi') }}" class="block px-4 py-2 text-sm text-amber-800 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-700">🇻🇳 Tiếng Việt</a>
            </div>
        </div>

        <button @click="toggleTheme()" class="px-3 py-2 rounded-full bg-amber-100 dark:bg-gray-800 text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-gray-700 transition-all">
            <i x-show="!isDarkMode" class="fas fa-moon text-lg"></i>
            <i x-show="isDarkMode" class="fas fa-sun text-lg" style="display: none;"></i>
        </button>
    </div>

    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 gradient-warm rounded-2xl shadow-xl mb-4">
                <a href="{{ route('home') }}">
                    <i class="fas fa-robot text-amber-900 text-4xl"></i>
                </a>
            </div>
            <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">{{ __('Welcome Back') }}</h1>
            <p class="text-amber-600 dark:text-amber-400 mt-2">{{ __('We missed you! ✨') }}</p>
        </div>

        <div class="card-warm p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Email') }}</label>
                    <input type="email" name="email" required class="input-warm" placeholder="hello@example.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Password') }}</label>
                    <input type="password" name="password" required class="input-warm" placeholder="••••••••">
                </div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-amber-500 rounded">
                    <span class="ml-2 text-sm text-amber-600 dark:text-amber-400">{{ __('Remember me') }}</span>
                </label>
                <button type="submit" class="btn-warm">{{ __('Sign In') }}</button>
            </form>
            <p class="mt-6 text-center text-sm text-amber-600 dark:text-amber-400">
                {{ __('New here?') }} <a href="{{ route('register') }}" class="font-semibold underline">{{ __('Join our family') }}</a>
            </p>
        </div>
        <p class="text-center text-amber-400 text-sm mt-8">&copy; {{ date('Y') }} SaaS AI Chatbot. {{ __('Spread kindness. 💝') }}</p>
    </div>

    <script>
        function themeManager() {
            return {
                isDarkMode: localStorage.getItem('theme') === 'dark',
                initTheme() {
                    if (this.isDarkMode) document.documentElement.classList.add('dark');
                },
                toggleTheme() {
                    this.isDarkMode = !this.isDarkMode;
                    if (this.isDarkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }
                }
            }
        }
    </script>
</body>
</html>
