<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" x-init="initTheme()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SaaS AI Chatbot')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* Light Mode Default Styles */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9eef5 100%);
            color: #1f2937;
        }

        /* Dark Mode Styles */
        .dark body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #f3f4f6;
        }

        .dark .bg-white {
            background-color: #1f2937 !important;
        }

        .dark .bg-gray-50,
        .dark .bg-gray-100 {
            background-color: #111827 !important;
        }

        .dark .text-gray-900,
        .dark .text-gray-800,
        .dark .text-gray-700 {
            color: #f3f4f6 !important;
        }

        .dark .text-gray-600,
        .dark .text-gray-500 {
            color: #9ca3af !important;
        }

        .dark .text-gray-400 {
            color: #6b7280 !important;
        }

        .dark .border-gray-200,
        .dark .border-gray-300 {
            border-color: #374151 !important;
        }

        .dark .bg-gray-50 {
            background-color: #1f2937 !important;
        }

        /* Ensure text is visible in light mode */
        .text-gray-900 {
            color: #111827 !important;
        }

        .text-gray-800 {
            color: #1f2937 !important;
        }

        .text-gray-700 {
            color: #374151 !important;
        }

        .text-gray-600 {
            color: #4b5563 !important;
        }

        .text-gray-500 {
            color: #6b7280 !important;
        }

        /* Dark mode text overrides */
        .dark .text-gray-900,
        .dark .text-gray-800,
        .dark .text-gray-700 {
            color: #f9fafb !important;
        }

        .dark .text-gray-600 {
            color: #d1d5db !important;
        }

        .dark .text-gray-500 {
            color: #9ca3af !important;
        }

        /* Icons color fix */
        i, .fas, .far, .fab {
            color: inherit;
        }

        .dark i, .dark .fas, .dark .far, .dark .fab {
            color: inherit;
        }

        /* Gradient text - ensure visibility */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dark .gradient-text {
            background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }

        .animate-pulse-slow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }

        .dark ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        /* Glass Morphism */
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .glass {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Card Hover Effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            color: white !important;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            transition: all 0.3s ease;
            color: white !important;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(240, 147, 251, 0.3);
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 3px solid #667eea;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.25rem;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            color: #374151;
            background: white;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .dark .pagination .page-link {
            background: #1f2937;
            color: #9ca3af;
            border-color: #374151;
        }

        .pagination .page-link:hover {
            background: #f3f4f6;
        }

        .dark .pagination .page-link:hover {
            background: #374151;
        }

        .pagination .active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border-color: transparent;
        }

        .pagination .disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Form Inputs */
        input, select, textarea {
            background-color: white !important;
            color: #1f2937 !important;
        }

        .dark input, .dark select, .dark textarea {
            background-color: #374151 !important;
            color: #f3f4f6 !important;
            border-color: #4b5563 !important;
        }

        input::placeholder, textarea::placeholder {
            color: #9ca3af !important;
        }

        .dark input::placeholder, .dark textarea::placeholder {
            color: #6b7280 !important;
        }

        /* Table Styles */
        table {
            background-color: white;
        }

        .dark table {
            background-color: #1f2937;
        }

        /* Alert Messages */
        .alert-success {
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
            color: #166534;
        }

        .dark .alert-success {
            background-color: #1f3a1f;
            border-left-color: #4ade80;
            color: #86efac;
        }

        .alert-error {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .dark .alert-error {
            background-color: #3f1e1e;
            border-left-color: #f87171;
            color: #fecaca;
        }

        /* Code blocks */
        code {
            background-color: #f3f4f6;
            color: #dc2626;
            padding: 0.125rem 0.375rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .dark code {
            background-color: #1f2937;
            color: #f87171;
        }
    </style>

    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false, userMenuOpen: false }"
      :class="{ 'dark': isDarkMode }"
      class="antialiased transition-colors duration-300">

    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-900 shadow-lg sticky top-0 z-50 border-b border-gray-200 dark:border-gray-700">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 ml-2 lg:ml-0">
                            <div class="gradient-primary rounded-xl p-2 shadow-lg">
                                <i class="fas fa-robot text-white text-xl"></i>
                            </div>
                            <span class="text-xl font-bold gradient-text hidden sm:inline-block">SaaS AI Chatbot</span>
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Theme Switcher -->
                        <button @click="toggleTheme()"
                                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i x-show="!isDarkMode" class="fas fa-moon text-lg"></i>
                            <i x-show="isDarkMode" class="fas fa-sun text-lg"></i>
                        </button>

                        <!-- Notifications -->
                        <button class="relative p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        </button>

                        <!-- User Menu -->
                        <div class="relative">
                            <button @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center space-x-3 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                                <div class="gradient-primary rounded-full w-8 h-8 flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hidden sm:inline">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                            </button>

                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg py-2 z-50 border border-gray-200 dark:border-gray-700 animate-slide-in">
                                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-user-circle w-5 mr-3"></i> Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-sign-out-alt w-5 mr-3"></i> Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar (Mobile) -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>

        <div class="flex">
            <!-- Sidebar -->
            <aside x-show="sidebarOpen"
                   @click.away="sidebarOpen = false"
                   class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-900 shadow-2xl z-40 transform transition-transform duration-300 lg:relative lg:translate-x-0"
                   :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
                <div class="p-6">
                    <nav class="space-y-2">
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'gradient-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }} transition-all">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('bots.index') }}"
                           class="flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('bots.*') ? 'gradient-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }} transition-all">
                            <i class="fas fa-robot w-5"></i>
                            <span>My Bots</span>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('users.index') }}"
                           class="flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('users.*') ? 'gradient-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }} transition-all">
                            <i class="fas fa-users w-5"></i>
                            <span>Users</span>
                        </a>
                        @endif
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-xl animate-fade-in-up">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                                <p class="text-green-700 dark:text-green-400">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-xl animate-fade-in-up">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                                <p class="text-red-700 dark:text-red-400">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user() && !auth()->user()->hasVerifiedEmail())
                        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded-r-xl animate-fade-in-up">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-yellow-500 text-xl mr-3"></i>
                                    <p class="text-yellow-700 dark:text-yellow-400">Your email is not verified. <a href="{{ route('verification.notice') }}" class="font-semibold underline hover:no-underline">Verify now</a></p>
                                </div>
                            </div>
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
                isDarkMode: false,
                initTheme() {
                    const savedTheme = localStorage.getItem('theme');
                    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        this.isDarkMode = true;
                        document.documentElement.classList.add('dark');
                    } else {
                        this.isDarkMode = false;
                        document.documentElement.classList.remove('dark');
                    }
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

    @stack('scripts')
</body>
</html>
