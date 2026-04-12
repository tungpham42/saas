<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" x-init="initTheme()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('SaaS AI Chatbot - Your Friendly AI Companion') }}</title>

    <meta name="description" content="{{ __('Multi-tenant AI Chatbot platform. Issue API keys, customize bots, Live Chat & History.') }}" />
    <meta property="og:image" content="@yield('og_image', asset('1200x630.jpg'))" />
    <meta property="og:image:type" content="image/jpg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:title" content="@yield('title', 'SaaS AI Chatbot')" />
    <meta property="og:description" content="{{ __('Multi-tenant AI Chatbot platform. Issue API keys, customize bots, Live Chat & History.') }}" />

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Light Mode */
        body {
            background: linear-gradient(135deg, #fef9e7 0%, #fff5e6 100%);
            color: #2c2418;
        }

        /* Dark Mode */
        .dark body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #e8e6e3;
        }

        .gradient-warm {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dark .gradient-text {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes gentleFade {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-gentle {
            animation: gentleFade 0.8s ease-out;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        .btn-warm {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #2c2418;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warm:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
        }

        .dark .btn-warm {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #fef3c7;
        }

        .btn-outline-warm {
            border: 2px solid #fbbf24;
            color: #f59e0b;
            background: transparent;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-warm:hover {
            background: rgba(251, 191, 36, 0.1);
            transform: translateY(-2px);
        }

        .dark .btn-outline-warm {
            border-color: #d97706;
            color: #fbbf24;
        }

        .card-warm {
            background: #ffffff;
            border-radius: 1.5rem;
            transition: all 0.3s ease;
        }

        .card-warm:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .dark .card-warm {
            background: #1f2937;
        }
    </style>
</head>
<body :class="{ 'dark': isDarkMode }" class="antialiased transition-colors duration-300">
    <nav class="fixed w-full bg-white/90 dark:bg-gray-900/90 backdrop-blur-md z-50 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="gradient-warm rounded-xl p-2 shadow-lg">
                        <i class="fas fa-robot text-amber-900 dark:text-amber-100 text-xl"></i>
                    </div>
                    <span class="ml-2 text-xl font-bold gradient-text">SaaS AI Chatbot</span>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="#features" class="text-amber-700 dark:text-amber-300 hover:text-amber-600 dark:hover:text-amber-400 transition font-medium">{{ __('Features') }}</a>
                    <a href="#pricing" class="text-amber-700 dark:text-amber-300 hover:text-amber-600 dark:hover:text-amber-400 transition font-medium">{{ __('Pricing') }}</a>

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

                    <a href="{{ route('login') }}" class="text-amber-700 dark:text-amber-300 hover:text-amber-600 transition">{{ __('Sign In') }}</a>
                    <a href="{{ route('register') }}" class="btn-warm px-5 py-2 rounded-full shadow-md">
                        {{ __('Get Started 💝') }}
                    </a>
                </div>

                <div class="md:hidden flex items-center space-x-3">
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen" @click.away="langOpen = false" class="px-3 py-2 rounded-full bg-amber-100 dark:bg-gray-800 text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-gray-700 transition-all">
                            <i class="fas fa-globe text-lg"></i>
                        </button>
                        <div x-show="langOpen" x-transition class="absolute right-0 mt-2 w-32 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-amber-100 dark:border-gray-700 overflow-hidden" style="display: none;">
                            <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-amber-800 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-700">🇺🇸 English</a>
                            <a href="{{ route('language.switch', 'vi') }}" class="block px-4 py-2 text-sm text-amber-800 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-gray-700">🇻🇳 Tiếng Việt</a>
                        </div>
                    </div>

                    <button @click="toggleTheme()" class="px-3 py-2 rounded-full bg-amber-100 dark:bg-gray-800 text-amber-600 dark:text-amber-400">
                        <i x-show="!isDarkMode" class="fas fa-moon text-lg"></i>
                        <i x-show="isDarkMode" class="fas fa-sun text-lg" style="display: none;"></i>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-amber-700 dark:text-amber-300 text-2xl">
                        <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>

            <div x-show="mobileMenuOpen" x-collapse class="md:hidden pb-4 space-y-2">
                <a href="#features" @click="mobileMenuOpen = false" class="block py-2 text-amber-700 dark:text-amber-300 hover:text-amber-600">{{ __('Features') }}</a>
                <a href="#pricing" @click="mobileMenuOpen = false" class="block py-2 text-amber-700 dark:text-amber-300 hover:text-amber-600">{{ __('Pricing') }}</a>
                <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="block py-2 text-amber-700 dark:text-amber-300 hover:text-amber-600">{{ __('Sign In') }}</a>
                <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="block py-2 btn-warm text-center rounded-full">{{ __('Get Started 💝') }}</a>
            </div>
        </div>
    </nav>

    <section class="pt-32 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center bg-amber-100 dark:bg-amber-900/30 rounded-full px-4 py-2 mb-6 animate-gentle">
                    <i class="fas fa-heart text-amber-600 dark:text-amber-400 mr-2"></i>
                    <span class="text-sm font-medium text-amber-800 dark:text-amber-300">{{ __('Launch your AI assistant in minutes') }}</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-amber-900 dark:text-amber-100 mb-6 animate-gentle">
                    {{ __('Your Friendly AI') }}
                    <span class="gradient-text">{{ __('Chatbot Companion') }}</span>
                </h1>
                <p class="text-xl text-amber-700 dark:text-amber-300 max-w-2xl mx-auto mb-10 animate-gentle delay-100">
                    {{ __('Deploy AI-powered chatbots across multiple channels. Engage customers 24/7, capture leads, and spread kindness. 🤗') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4 animate-gentle delay-200">
                    <a href="{{ route('register') }}" class="btn-warm px-8 py-4 rounded-full font-semibold text-lg shadow-lg inline-flex items-center gap-2">
                        <i class="fas fa-rocket"></i>
                        {{ __('Start Free Trial') }}
                    </a>
                    <a href="#features" class="btn-outline-warm px-8 py-4 rounded-full font-semibold text-lg inline-flex items-center gap-2">
                        <i class="fas fa-play"></i>
                        {{ __('Learn More') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-20 bg-white/50 dark:bg-gray-900/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-amber-900 dark:text-amber-100">{{ __('Why You\'ll Love Us 💝') }}</h2>
                <p class="text-xl text-amber-600 dark:text-amber-400 mt-4">{{ __('Everything you need to delight your customers') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card-warm p-8 shadow-lg hover:shadow-xl transition-all text-center">
                    <div class="gradient-warm rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fab fa-facebook-messenger text-amber-900 dark:text-amber-100 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-amber-800 dark:text-amber-200 mb-2">{{ __('Multi-Channel Love') }}</h3>
                    <p class="text-amber-600 dark:text-amber-400">{{ __('Connect Facebook, Zalo, WhatsApp, TikTok, and more. All conversations in one cozy place.') }}</p>
                </div>

                <div class="card-warm p-8 shadow-lg hover:shadow-xl transition-all text-center">
                    <div class="gradient-warm rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-brain text-amber-900 dark:text-amber-100 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-amber-800 dark:text-amber-200 mb-2">{{ __('Smart Knowledge Base') }}</h3>
                    <p class="text-amber-600 dark:text-amber-400">{{ __('Upload documents, PDFs, and websites. Your AI learns from your content to give wise answers.') }}</p>
                </div>

                <div class="card-warm p-8 shadow-lg hover:shadow-xl transition-all text-center">
                    <div class="gradient-warm rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-heart text-amber-900 dark:text-amber-100 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-amber-800 dark:text-amber-200 mb-2">{{ __('Live Chat & Love') }}</h3>
                    <p class="text-amber-600 dark:text-amber-400">{{ __('Real-time chat with human takeover. Track conversations, leads, and spread joy.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-amber-900 dark:text-amber-100">{{ __('Simple & Fair Pricing 💰') }}</h2>
                <p class="text-xl text-amber-600 dark:text-amber-400 mt-4">{{ __('Start free, grow with us') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="card-warm p-8 shadow-lg hover:shadow-xl transition-all flex flex-col">
                    <h3 class="text-2xl font-bold text-amber-800 dark:text-amber-200 mb-4">{{ __('Standard - Tiêu chuẩn') }}</h3>
                    <div class="text-4xl font-bold text-amber-900 dark:text-amber-100 mb-2">
                        300,000 <span class="text-lg text-amber-600 dark:text-amber-400">VND / {{ __('per month') }}</span>
                    </div>
                    <ul class="mt-6 space-y-4 flex-1 text-left">
                        <li class="flex items-start text-amber-700 dark:text-amber-300">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>{{ __('CSKH đa kênh 24/7/365') }}</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 btn-outline-warm px-6 py-3 rounded-full text-center font-semibold w-full block">
                        {{ __('Get Started 💝') }}
                    </a>
                </div>

                <div class="card-warm p-8 shadow-lg hover:shadow-xl transition-all flex flex-col relative border-2 border-amber-400 dark:border-amber-600">
                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <span class="gradient-warm text-amber-900 font-bold px-4 py-1 rounded-full text-sm shadow-md whitespace-nowrap">
                            {{ __('🌟 MOST POPULAR 🌟') }}
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-amber-800 dark:text-amber-200 mb-4">{{ __('Custom - Tùy chỉnh') }}</h3>
                    <div class="text-4xl font-bold text-amber-900 dark:text-amber-100 mb-2">
                        700,000 <span class="text-lg text-amber-600 dark:text-amber-400">VND / {{ __('per month') }}</span>
                    </div>
                    <ul class="mt-6 space-y-4 flex-1 text-left">
                        <li class="flex items-start text-amber-700 dark:text-amber-300">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>{{ __('Bao gồm gói Standard') }}</span>
                        </li>
                        <li class="flex items-start text-amber-700 dark:text-amber-300">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>{{ __('Tùy chỉnh theo yêu cầu') }}</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 btn-warm px-6 py-3 rounded-full text-center font-semibold w-full block shadow-lg">
                        {{ __('Get Started 💝') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-amber-900 dark:bg-gray-950 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="gradient-warm rounded-xl p-2">
                            <i class="fas fa-robot text-amber-900 text-lg"></i>
                        </div>
                        <span class="ml-2 text-lg font-bold text-amber-100">SaaS AI Chatbot</span>
                    </div>
                    <p class="text-amber-300 text-sm">{{ __('Spreading kindness, one chat at a time. 💝') }}</p>
                </div>
                <div>
                    <h4 class="text-amber-100 font-semibold mb-3">{{ __('Product') }}</h4>
                    <ul class="space-y-2 text-sm text-amber-300">
                        <li><a href="#features" class="hover:text-amber-200 transition">{{ __('Features') }}</a></li>
                        <li><a href="#pricing" class="hover:text-amber-200 transition">{{ __('Pricing') }}</a></li>
                        <li><a href="#" class="hover:text-amber-200 transition">{{ __('Documentation') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-amber-100 font-semibold mb-3">{{ __('Company') }}</h4>
                    <ul class="space-y-2 text-sm text-amber-300">
                        <li><a href="#" class="hover:text-amber-200 transition">{{ __('About Us') }}</a></li>
                        <li><a href="#" class="hover:text-amber-200 transition">{{ __('Blog') }}</a></li>
                        <li><a href="#" class="hover:text-amber-200 transition">{{ __('Contact') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-amber-100 font-semibold mb-3">{{ __('Legal') }}</h4>
                    <ul class="space-y-2 text-sm text-amber-300">
                        <li><a href="#" class="hover:text-amber-200 transition">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="#" class="hover:text-amber-200 transition">{{ __('Terms of Service') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-amber-800 pt-8 text-center">
                <p class="text-amber-300 text-sm">&copy; {{ date('Y') }} SaaS AI Chatbot. {{ __('Made with 💝 around the world.') }}</p>
            </div>
        </div>
    </footer>

    <script>
        function themeManager() {
            return {
                isDarkMode: false,
                mobileMenuOpen: false,
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
</body>
</html>
