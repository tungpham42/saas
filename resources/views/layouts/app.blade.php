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
    <meta property="og:image:type" content="image/jpg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:title" content="@yield('title', 'SaaS AI Chatbot')" />
    <meta property="og:description" content="Multi-tenant AI Chatbot platform. Issue API keys, customize bots, Live Chat & History." />

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

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
    </style>

    @stack('styles')
    <script>
    (function() {
        const theme = localStorage.getItem('theme');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
    })();
    </script>
</head>
<body x-data="{ sidebarOpen: false, userMenuOpen: false }"
      class="antialiased transition-all duration-300">

    <div class="min-h-screen">
        <!-- Navigation - Warm & Cozy -->
        <nav id="topnav" class="fixed w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-sm z-50 transition-all duration-300 border-b border-amber-100 dark:border-amber-900/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-xl text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all">
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 ml-2 lg:ml-0 group">
                            <div class="gradient-warm rounded-xl p-2 shadow-lg group-hover:scale-105 transition-transform">
                                <i class="fas fa-robot text-amber-900 dark:text-amber-100 text-xl"></i>
                            </div>
                            <span class="text-xl font-bold bg-gradient-to-r from-amber-600 to-orange-500 bg-clip-text text-transparent">SaaS AI Chatbot</span>
                        </a>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Theme Switcher -->
                        <button @click="toggleTheme()"
                                class="p-2 rounded-xl bg-amber-50 dark:bg-gray-800 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-gray-700 transition-all">
                            <i x-show="!isDarkMode" class="fas fa-moon text-lg"></i>
                            <i x-show="isDarkMode" class="fas fa-sun text-lg"></i>
                        </button>

                        <!-- Notifications -->
                        <button class="relative p-2 rounded-xl bg-amber-50 dark:bg-gray-800 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-gray-700 transition-all">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-orange-500 rounded-full animate-pulse-soft"></span>
                        </button>

                        <!-- User Menu -->
                        <div class="relative">
                            <button @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center space-x-3 p-2 rounded-xl bg-amber-50 dark:bg-gray-800 hover:bg-amber-100 dark:hover:bg-gray-700 transition-all">
                                <div class="gradient-warm rounded-full w-8 h-8 flex items-center justify-center">
                                    <span class="text-amber-900 dark:text-amber-100 text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                </div>
                                <span class="text-sm font-medium text-amber-800 dark:text-amber-200 hidden sm:inline">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-amber-500"></i>
                            </button>

                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-2xl shadow-lg py-2 z-50 border border-amber-100 dark:border-gray-700 animate-gentle">
                                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm text-amber-800 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-700">
                                    <i class="fas fa-user-circle w-5 mr-3 text-amber-500"></i> Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-amber-50 dark:hover:bg-gray-700">
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
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm z-40 lg:hidden"></div>

        <div class="flex pt-16">
            <!-- Sidebar - Warm & Cozy -->
            <aside x-show="sidebarOpen"
                   @click.away="sidebarOpen = false"
                   class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-900 shadow-xl z-40 transform transition-transform duration-300 lg:relative lg:translate-x-0 border-r border-amber-100 dark:border-gray-800"
                   :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
                <div class="p-6">
                    <nav class="space-y-2">
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'gradient-warm text-amber-900 dark:text-amber-100 shadow-md' : 'text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-gray-800' }}">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('bots.index') }}"
                           class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('bots.*') ? 'gradient-warm text-amber-900 dark:text-amber-100 shadow-md' : 'text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-gray-800' }}">
                            <i class="fas fa-robot w-5"></i>
                            <span>My Bots</span>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('users.index') }}"
                           class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('users.*') ? 'gradient-warm text-amber-900 dark:text-amber-100 shadow-md' : 'text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-gray-800' }}">
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
                        <div class="mb-6 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-4 rounded-r-2xl animate-gentle">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-amber-500 text-xl mr-3"></i>
                                <p class="text-amber-700 dark:text-amber-400">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-2xl animate-gentle">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                                <p class="text-red-700 dark:text-red-400">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user() && !auth()->user()->hasVerifiedEmail())
                        <div class="mb-6 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded-r-2xl animate-gentle">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-orange-500 text-xl mr-3"></i>
                                <p class="text-orange-700 dark:text-orange-400">Your email is not verified. <a href="{{ route('verification.notice') }}" class="font-semibold underline hover:no-underline">Verify now</a></p>
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
