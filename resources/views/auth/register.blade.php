<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" x-init="initTheme()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Join Us') }} - SaaS AI Chatbot</title>
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
        .btn-warm { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); font-weight: 600; padding: 0.75rem; border-radius: 1rem; width: 100%; }
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
            <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">{{ __('Join Our Family') }}</h1>
            <p class="text-amber-600 dark:text-amber-400 mt-2">{{ __('Start your AI journey with us 🚀') }}</p>
        </div>

        <div class="card-warm p-8">
            <form method="POST" action="{{ route('register') }}" x-data="registerForm()">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('What should we call you?') }}</label>
                        <input type="text" name="name" x-model="form.name" required class="input-warm" placeholder="{{ __('e.g., Sarah Johnson') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Your Email') }}</label>
                        <input type="email" name="email" x-model="form.email" required class="input-warm" placeholder="hello@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Create Password') }}</label>
                        <input type="password" name="password" x-model="form.password" required class="input-warm" placeholder="{{ __('Make it secure') }}">
                        <div class="mt-2 flex flex-wrap gap-2 text-[10px]">
                            <span :class="form.password.length >= 8 ? 'text-green-500' : 'text-amber-500'"><i class="fas fa-circle mr-1"></i>{{ __('8+ chars') }}</span>
                            <span :class="/[0-9]/.test(form.password) ? 'text-green-500' : 'text-amber-500'"><i class="fas fa-circle mr-1"></i>{{ __('Number') }}</span>
                            <span :class="/[A-Z]/.test(form.password) && /[a-z]/.test(form.password) ? 'text-green-500' : 'text-amber-500'"><i class="fas fa-circle mr-1"></i>{{ __('Mixed case') }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Confirm Password') }}</label>
                        <input type="password" name="password_confirmation" x-model="form.password_confirmation" required class="input-warm" placeholder="{{ __('Type it again') }}">
                        <p x-show="form.password && form.password_confirmation && form.password !== form.password_confirmation" class="text-xs text-red-500 mt-1">{{ __('Passwords don\'t match') }}</p>
                    </div>
                    <label class="flex items-start text-xs text-amber-600 dark:text-amber-400">
                        <input type="checkbox" x-model="form.terms" required class="mt-0.5 mr-2">
                        <span>{{ __('I agree to the') }} <a href="#" class="underline">{{ __('Terms of Service') }}</a> {{ __('and') }} <a href="#" class="underline">{{ __('Privacy Policy') }}</a></span>
                    </label>
                </div>
                <button type="submit" :disabled="!isFormValid" class="btn-warm mt-6 disabled:opacity-50">{{ __('Create Account') }}</button>
            </form>
            <p class="mt-6 text-center text-sm text-amber-600 dark:text-amber-400">
                {{ __('Already with us?') }} <a href="{{ route('login') }}" class="font-semibold underline">{{ __('Sign in') }}</a>
            </p>
        </div>
    </div>

    <script>
        function themeManager() {
            return {
                isDarkMode: localStorage.getItem('theme') === 'dark',
                initTheme() { if (this.isDarkMode) document.documentElement.classList.add('dark'); },
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
        function registerForm() {
            return {
                form: { name: '', email: '', password: '', password_confirmation: '', terms: false },
                get isFormValid() { return this.form.name && this.form.email && this.form.password.length >= 8 && this.form.password === this.form.password_confirmation && this.form.terms; }
            }
        }
    </script>
</body>
</html>
