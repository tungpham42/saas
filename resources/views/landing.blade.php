<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" x-init="initTheme()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SaaS AI Chatbot - Your Friendly AI Companion</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
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
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/90 dark:bg-gray-900/90 backdrop-blur-md z-50 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="gradient-warm rounded-xl p-2 shadow-lg">
                        <i class="fas fa-robot text-amber-900 dark:text-amber-100 text-xl"></i>
                    </div>
                    <span class="ml-2 text-xl font-bold gradient-text">SaaS AI Chatbot</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#features" class="text-amber-700 dark:text-amber-300 hover:text-amber-600 dark:hover:text-amber-400 transition font-medium">Features</a>
                    <a href="#pricing" class="text-amber-700 dark:text-amber-300 hover:text-amber-600 dark:hover:text-amber-400 transition font-medium">Pricing</a>

                    <!-- Theme Switcher -->
                    <button @click="toggleTheme()"
                            class="p-2 rounded-xl bg-amber-100 dark:bg-gray-800 text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-gray-700 transition-all">
                        <i x-show="!isDarkMode" class="fas fa-moon text-lg"></i>
                        <i x-show="isDarkMode" class="fas fa-sun text-lg"></i>
                    </button>

                    <a href="{{ route('login') }}" class="text-amber-700 dark:text-amber-300 hover:text-amber-600 transition">Sign In</a>
                    <a href="{{ route('register') }}" class="btn-warm px-5 py-2 rounded-full shadow-md">
                        Get Started 💝
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-3">
                    <button @click="toggleTheme()"
                            class="p-2 rounded-xl bg-amber-100 dark:bg-gray-800 text-amber-600 dark:text-amber-400">
                        <i x-show="!isDarkMode" class="fas fa-moon text-lg"></i>
                        <i x-show="isDarkMode" class="fas fa-sun text-lg"></i>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-amber-700 dark:text-amber-300 text-2xl">
                        <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-collapse class="md:hidden pb-4 space-y-2">
                <a href="#features" @click="mobileMenuOpen = false" class="block py-2 text-amber-700 dark:text-amber-300 hover:text-amber-600">Features</a>
                <a href="#pricing" @click="mobileMenuOpen = false" class="block py-2 text-amber-700 dark:text-amber-300 hover:text-amber-600">Pricing</a>
                <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="block py-2 text-amber-700 dark:text-amber-300 hover:text-amber-600">Sign In</a>
                <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="block py-2 btn-warm text-center rounded-full">Get Started 💝</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center bg-amber-100 dark:bg-amber-900/30 rounded-full px-4 py-2 mb-6 animate-gentle">
                    <i class="fas fa-heart text-amber-600 dark:text-amber-400 mr-2"></i>
                    <span class="text-sm font-medium text-amber-800 dark:text-amber-300">Launch your AI assistant in minutes</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-amber-900 dark:text-amber-100 mb-6 animate-gentle">
                    Your Friendly AI
                    <span class="gradient-text">Chatbot Companion</span>
                </h1>
                <p class="text-xl text-amber-700 dark:text-amber-300 max-w-2xl mx-auto mb-10 animate-gentle delay-100">
                    Deploy AI-powered chatbots across multiple channels. Engage customers 24/7, capture leads, and spread kindness. 🤗
                </p>
                <div class="flex flex-wrap justify-center gap-4 animate-gentle delay-200">
                    <a href="{{ route('register') }}" class="btn-warm px-8 py-4 rounded-full font-semibold text-lg shadow-lg inline-flex items-center gap-2">
                        <i class="fas fa-rocket"></i>
                        Start Free Trial
                    </a>
                    <a href="#features" class="btn-outline-warm px-8 py-4 rounded-full font-semibold text-lg inline-flex items-center gap-2">
                        <i class="fas fa-play"></i>
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white/50 dark:bg-gray-900/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-amber-900 dark:text-amber-100">Why You'll Love Us 💝</h2>
                <p class="text-xl text-amber-600 dark:text-amber-400 mt-4">Everything you need to delight your customers</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card-warm p-8 shadow-lg hover:shadow-xl transition-all text-center">
                    <div class="gradient-warm rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fab fa-facebook-messenger text-amber-900 dark:text-amber-100 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-amber-800 dark:text-amber-200 mb-2">Multi-Channel Love</h3>
                    <p class="text-amber-600 dark:text-amber-400">Connect Facebook, Zalo, WhatsApp, TikTok, and more. All conversations in one cozy place.</p>
                </div>

                <div class="card-warm p-8 shadow-lg hover:shadow-xl transition-all text-center">
                    <div class="gradient-warm rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-brain text-amber-900 dark:text-amber-100 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-amber-800 dark:text-amber-200 mb-2">Smart Knowledge Base</h3>
                    <p class="text-amber-600 dark:text-amber-400">Upload documents, PDFs, and websites. Your AI learns from your content to give wise answers.</p>
                </div>

                <div class="card-warm p-8 shadow-lg hover:shadow-xl transition-all text-center">
                    <div class="gradient-warm rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-heart text-amber-900 dark:text-amber-100 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-amber-800 dark:text-amber-200 mb-2">Live Chat & Love</h3>
                    <p class="text-amber-600 dark:text-amber-400">Real-time chat with human takeover. Track conversations, leads, and spread joy.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="animate-gentle">
                    <div class="text-4xl font-bold text-amber-600 dark:text-amber-400">10K+</div>
                    <p class="text-amber-500 dark:text-amber-500 mt-2">Happy Users</p>
                </div>
                <div class="animate-gentle delay-100">
                    <div class="text-4xl font-bold text-amber-600 dark:text-amber-400">1M+</div>
                    <p class="text-amber-500 dark:text-amber-500 mt-2">Messages Shared</p>
                </div>
                <div class="animate-gentle delay-200">
                    <div class="text-4xl font-bold text-amber-600 dark:text-amber-400">99.9%</div>
                    <p class="text-amber-500 dark:text-amber-500 mt-2">Uptime Love</p>
                </div>
                <div class="animate-gentle delay-300">
                    <div class="text-4xl font-bold text-amber-600 dark:text-amber-400">24/7</div>
                    <p class="text-amber-500 dark:text-amber-500 mt-2">Support & Care</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white/50 dark:bg-gray-900/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-amber-900 dark:text-amber-100">Simple & Fair Pricing 💰</h2>
                <p class="text-xl text-amber-600 dark:text-amber-400 mt-4">Start free, grow with us</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Free Plan -->
                <div class="card-warm overflow-hidden shadow-lg hover:shadow-xl transition-all">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-gift text-amber-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-amber-800 dark:text-amber-200">Free</h3>
                        <p class="text-4xl font-bold text-amber-900 dark:text-amber-100 mt-4">$0</p>
                        <p class="text-amber-500">per month</p>
                        <ul class="mt-6 space-y-3 text-left">
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> 1 Bot</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Basic AI Models</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Website Widget</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> 30 Days History</li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-8 block btn-outline-warm px-4 py-2 rounded-full text-center">Get Started 💝</a>
                    </div>
                </div>

                <!-- Pro Plan - Most Popular -->
                <div class="card-warm overflow-hidden shadow-2xl transform scale-105 border-2 border-amber-400 dark:border-amber-600">
                    <div class="gradient-warm text-amber-900 text-center py-2">
                        <span class="text-sm font-semibold">🌟 MOST POPULAR 🌟</span>
                    </div>
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-star text-amber-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-amber-800 dark:text-amber-200">Pro</h3>
                        <p class="text-4xl font-bold text-amber-900 dark:text-amber-100 mt-4">$29</p>
                        <p class="text-amber-500">per month</p>
                        <ul class="mt-6 space-y-3 text-left">
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Up to 5 Bots</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Advanced AI Models</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Multi-channel Support</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Knowledge Base (RAG)</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Analytics & Reports</li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-8 block btn-warm px-4 py-2 rounded-full text-center">Start Free Trial 🚀</a>
                    </div>
                </div>

                <!-- Business Plan -->
                <div class="card-warm overflow-hidden shadow-lg hover:shadow-xl transition-all">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-building text-amber-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-amber-800 dark:text-amber-200">Business</h3>
                        <p class="text-4xl font-bold text-amber-900 dark:text-amber-100 mt-4">$99</p>
                        <p class="text-amber-500">per month</p>
                        <ul class="mt-6 space-y-3 text-left">
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Unlimited Bots</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Priority Support</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> Custom AI Training</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> API Access</li>
                            <li class="flex items-center gap-2 text-amber-700 dark:text-amber-300"><i class="fas fa-check-circle text-green-500"></i> White-label Option</li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-8 block btn-outline-warm px-4 py-2 rounded-full text-center">Contact Sales 💼</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-amber-900 dark:text-amber-100">What Our Community Says 💬</h2>
                <p class="text-xl text-amber-600 dark:text-amber-400 mt-4">Loved by businesses worldwide</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card-warm p-6 shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="gradient-warm rounded-full w-12 h-12 flex items-center justify-center">
                            <span class="text-amber-900 font-bold">JD</span>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-800 dark:text-amber-200">Sarah Johnson</p>
                            <div class="flex text-amber-400">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-amber-600 dark:text-amber-400">"This platform transformed our customer support! Our customers love the friendly AI assistant. Best decision ever!"</p>
                </div>

                <div class="card-warm p-6 shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="gradient-warm rounded-full w-12 h-12 flex items-center justify-center">
                            <span class="text-amber-900 font-bold">MK</span>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-800 dark:text-amber-200">Michael Chen</p>
                            <div class="flex text-amber-400">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-amber-600 dark:text-amber-400">"The multi-channel support is incredible! We're now available on WhatsApp, Facebook, and our website all in one place."</p>
                </div>

                <div class="card-warm p-6 shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="gradient-warm rounded-full w-12 h-12 flex items-center justify-center">
                            <span class="text-amber-900 font-bold">ER</span>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-800 dark:text-amber-200">Emma Rodriguez</p>
                            <div class="flex text-amber-400">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-amber-600 dark:text-amber-400">"The knowledge base feature is amazing! Our AI learned everything about our products and answers questions perfectly."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="gradient-warm py-20">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-3xl font-bold text-amber-900 mb-4">Ready to Spread Kindness? 💝</h2>
            <p class="text-amber-800 text-lg mb-8">Join thousands of businesses already using AI to delight their customers.</p>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-amber-600 px-8 py-4 rounded-full font-semibold text-lg hover:shadow-xl transition transform hover:scale-105">
                Start Free Trial
                <i class="fas fa-heart"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
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
                    <p class="text-amber-300 text-sm">Spreading kindness, one chat at a time. 💝</p>
                </div>
                <div>
                    <h4 class="text-amber-100 font-semibold mb-3">Product</h4>
                    <ul class="space-y-2 text-sm text-amber-300">
                        <li><a href="#features" class="hover:text-amber-200 transition">Features</a></li>
                        <li><a href="#pricing" class="hover:text-amber-200 transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-amber-200 transition">Documentation</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-amber-100 font-semibold mb-3">Company</h4>
                    <ul class="space-y-2 text-sm text-amber-300">
                        <li><a href="#" class="hover:text-amber-200 transition">About Us</a></li>
                        <li><a href="#" class="hover:text-amber-200 transition">Blog</a></li>
                        <li><a href="#" class="hover:text-amber-200 transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-amber-100 font-semibold mb-3">Legal</h4>
                    <ul class="space-y-2 text-sm text-amber-300">
                        <li><a href="#" class="hover:text-amber-200 transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-amber-200 transition">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-amber-800 pt-8 text-center">
                <p class="text-amber-300 text-sm">&copy; {{ date('Y') }} SaaS AI Chatbot. Made with 💝 around the world.</p>
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
