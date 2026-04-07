<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="themeManager()"
      x-init="initTheme()"
      :class="{ 'dark': isDarkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SaaS AI Chatbot')</title>

    <meta name="description" content="Multi-tenant AI Chatbot platform. Issue API keys, customize bots, Live Chat & History." />
    <meta property="og:image" content="@yield('og_image', asset('1200x630.jpg'))" />
    <meta property="og:title" content="@yield('title', 'SaaS AI Chatbot')" />

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* Warm Light Mode */
        body {
            background: linear-gradient(135deg, #fef9e7 0%, #fff5e6 100%);
            color: #2c2418;
        }

        .dark body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #e8e6e3;
        }

        /* Glassmorphism Card Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(251, 191, 36, 0.2);
            border-radius: 1.5rem;
        }

        .dark .glass-card {
            background: rgba(30, 30, 46, 0.9);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        /* Warm Card Background */
        .card-warm {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .dark .card-warm {
            background: #1f2937;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .card-warm:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        /* Warm Gradient */
        .gradient-warm {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .gradient-warm-dark {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }

        /* Soft Buttons */
        .btn-soft {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #2c2418;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-soft:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
        }

        .dark .btn-soft {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #fef3c7;
        }

        .btn-outline-soft {
            border: 2px solid #fbbf24;
            color: #f59e0b;
            background: transparent;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 1rem;
            transition: all 0.3s ease;
        }

        .btn-outline-soft:hover {
            background: rgba(251, 191, 36, 0.1);
            transform: translateY(-2px);
        }

        .dark .btn-outline-soft {
            border-color: #d97706;
            color: #fbbf24;
        }

        /* Text Colors - Warm Tones */
        .text-warm {
            color: #78350f;
        }

        .dark .text-warm {
            color: #fde68a;
        }

        /* Input Fields */
        .input-warm {
            background-color: #fffbeb;
            border: 2px solid #fde68a;
            border-radius: 1rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .input-warm:focus {
            border-color: #f59e0b;
            outline: none;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }

        .dark .input-warm {
            background-color: #374151;
            border-color: #d97706;
            color: #fef3c7;
        }

        /* Animations */
        @keyframes gentleFade {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes softPulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
        }

        .animate-gentle {
            animation: gentleFade 0.5s ease-out;
        }

        .animate-pulse-soft {
            animation: softPulse 2s ease-in-out infinite;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #fef3c7;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-radius: 10px;
        }

        .dark ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        /* Status Indicators */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active {
            background: #fef3c7;
            color: #b45309;
        }

        .dark .status-active {
            background: #451a03;
            color: #fde68a;
        }

        /* Table Styles */
        .table-warm {
            width: 100%;
            border-collapse: collapse;
        }

        .table-warm th {
            background: #fef3c7;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #78350f;
        }

        .dark .table-warm th {
            background: #2d2d44;
            color: #fde68a;
        }

        .table-warm td {
            padding: 1rem;
            border-bottom: 1px solid #fef3c7;
            color: #5b3b1c;
        }

        .dark .table-warm td {
            border-bottom-color: #374151;
            color: #e8e6e3;
        }

        .table-warm tr:hover td {
            background: #fffbeb;
        }

        .dark .table-warm tr:hover td {
            background: #2d2d44;
        }

        @keyframes pulse-session {
            0% {
                background-color: rgba(245, 158, 11, 0);
                border-left-color: rgba(245, 158, 11, 0);
            }
            50% {
                background-color: rgba(245, 158, 11, 0.1);
                border-left-color: rgba(245, 158, 11, 0.5);
            }
            100% {
                background-color: rgba(245, 158, 11, 0);
                border-left-color: rgba(245, 158, 11, 0);
            }
        }

        .animate-pulse-session {
            animation: pulse-session 2s ease-out;
            border-left: 3px solid transparent;
            position: relative;
        }

        /* Optional: Add a notification badge */
        .new-session-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f59e0b;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
        }

        /* Loading indicator for infinite scroll */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(245, 158, 11, 0.3);
            border-radius: 50%;
            border-top-color: #f59e0b;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .sessions-loading {
            text-align: center;
            padding: 20px;
            color: #f59e0b;
        }

        /* Sentinels for infinite scroll */
        #sessions-sentinel {
            opacity: 0;
            pointer-events: none;
        }

        /* Loading more indicator */
        .loading-more {
            text-align: center;
            padding: 12px;
            color: #f59e0b;
            font-size: 12px;
        }

        @keyframes flashNew {
            0% { background-color: #dcfce7; }
            100% { background-color: transparent; }
        }

        .new-session {
            animation: flashNew 1s ease-in-out;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false, userMenuOpen: false, langMenuOpen: false }" class="antialiased">

    <div class="min-h-screen">
        <nav class="fixed w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-sm z-50 border-b border-amber-100 dark:border-amber-900/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden px-3 py-2 text-amber-600 dark:text-amber-400">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 ml-2 group">
                            <div class="gradient-warm rounded-xl p-2 shadow-lg"><i class="fas fa-robot text-amber-900 text-xl"></i></div>
                            <span class="text-xl font-bold bg-gradient-to-r from-amber-600 to-orange-500 bg-clip-text text-transparent">SaaS AI Chatbot</span>
                        </a>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <button @click="langMenuOpen = !langMenuOpen" class="flex items-center space-x-2 px-3 py-2 rounded-full bg-amber-50 dark:bg-gray-800 text-amber-600 dark:text-amber-400">
                                <i class="fas fa-globe"></i>
                                <span class="text-sm font-medium uppercase">{{ app()->getLocale() }}</span>
                            </button>
                            <div x-show="langMenuOpen" @click.away="langMenuOpen = false" x-cloak class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-2xl shadow-lg py-2 border border-amber-100 dark:border-gray-700">
                                <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm {{ app()->getLocale() == 'en' ? 'text-amber-600 font-bold' : '' }}">🇺🇸 English</a>
                                <a href="{{ route('language.switch', 'vi') }}" class="block px-4 py-2 text-sm {{ app()->getLocale() == 'vi' ? 'text-amber-600 font-bold' : '' }}">🇻🇳 Tiếng Việt</a>
                            </div>
                        </div>

                        <button @click="toggleTheme()" class="px-3 py-2 rounded-full bg-amber-50 dark:bg-gray-800 text-amber-600 dark:text-amber-400">
                            <i x-show="!isDarkMode" class="fas fa-moon"></i>
                            <i x-show="isDarkMode" class="fas fa-sun" x-cloak></i>
                        </button>

                        @auth
                        <div class="relative">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-3 px-3 py-2 rounded-full bg-amber-50 dark:bg-gray-800">
                                <div class="gradient-warm rounded-full w-8 h-8 flex items-center justify-center text-amber-900 font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <i class="fas fa-chevron-down text-xs text-amber-500"></i>
                            </button>
                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-2xl shadow-lg py-2 border border-amber-100 dark:border-gray-700">
                                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm text-amber-800 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-700">
                                    <i class="fas fa-user-circle w-5 mr-3 text-amber-500"></i> {{ __('Profile') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-amber-50 dark:hover:bg-gray-700">
                                        <i class="fas fa-sign-out-alt w-5 mr-3"></i> {{ __('Sign out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex pt-16">
            <aside x-show="sidebarOpen" class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-900 shadow-xl z-40 lg:relative lg:translate-x-0 border-r border-amber-100 dark:border-gray-800" :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
                <div class="p-6">
                    <nav class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'gradient-warm text-amber-900 shadow-md' : 'text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-gray-800' }}">
                            <i class="fas fa-chart-line w-5"></i><span>{{ __('Dashboard') }}</span>
                        </a>
                        <a href="{{ route('bots.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('bots.*') ? 'gradient-warm text-amber-900 shadow-md' : 'text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-gray-800' }}">
                            <i class="fas fa-robot w-5"></i><span>{{ __('My Bots') }}</span>
                        </a>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('users.*') ? 'gradient-warm text-amber-900 shadow-md' : 'text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-gray-800' }}">
                            <i class="fas fa-users w-5"></i><span>{{ __('Users') }}</span>
                        </a>
                        @endif
                    </nav>
                </div>
            </aside>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @if(auth()->user() && !auth()->user()->hasVerifiedEmail())
                        <div class="mb-6 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded-r-2xl animate-gentle">
                            <p class="text-orange-700 dark:text-orange-400">
                                {{ __('Your email is not verified.') }}
                                <a href="{{ route('verification.notice') }}" class="font-semibold underline">{{ __('Verify now') }}</a>
                            </p>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        function themeManager() {
            return {
                isDarkMode: localStorage.getItem('theme') === 'dark',
                initTheme() { if (this.isDarkMode) document.documentElement.classList.add('dark'); },
                toggleTheme() {
                    this.isDarkMode = !this.isDarkMode;
                    document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
                }
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
