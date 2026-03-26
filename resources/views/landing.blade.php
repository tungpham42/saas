<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SaaS AI Chatbot - Intelligent Customer Support Platform</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
    </style>
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-md z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="gradient-bg rounded-xl p-2">
                        <i class="fas fa-robot text-white text-xl"></i>
                    </div>
                    <span class="ml-2 text-xl font-bold gradient-text">SaaS AI Chatbot</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 font-medium">Sign In</a>
                    <a href="{{ route('register') }}" class="gradient-bg text-white px-5 py-2 rounded-full font-medium hover:opacity-90 transition">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center bg-gradient-to-r from-blue-50 to-purple-50 rounded-full px-4 py-2 mb-6 animate-fade-in-up">
                    <i class="fas fa-rocket text-purple-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700">Launch your AI assistant in minutes</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 animate-fade-in-up">
                    Intelligent AI Chatbot
                    <span class="gradient-text">for Your Business</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-10 animate-fade-in-up delay-100">
                    Deploy AI-powered chatbots across multiple channels. Engage customers 24/7, capture leads, and boost sales.
                </p>
                <div class="flex flex-wrap justify-center gap-4 animate-fade-in-up delay-200">
                    <a href="{{ route('register') }}" class="gradient-bg text-white px-8 py-4 rounded-full font-semibold text-lg hover:opacity-90 transition transform hover:scale-105">
                        Start Free Trial
                    </a>
                    <a href="#features" class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-full font-semibold text-lg hover:border-purple-600 hover:text-purple-600 transition">
                        Learn More
                    </a>
                </div>
                <div class="mt-12 animate-fade-in-up delay-300">
                    <img src="https://placehold.co/800x400/667eea/white?text=AI+Chatbot+Demo" alt="Dashboard Preview" class="rounded-2xl shadow-2xl mx-auto float-animation">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Powerful Features</h2>
                <p class="text-xl text-gray-600 mt-4">Everything you need to deliver exceptional customer support</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition text-center card-hover">
                    <div class="gradient-bg rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-facebook-messenger text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Multi-Channel Support</h3>
                    <p class="text-gray-600">Connect Facebook, Zalo, WhatsApp, TikTok, and more. Manage all conversations in one place.</p>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition text-center card-hover">
                    <div class="gradient-bg rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-brain text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Knowledge Base (RAG)</h3>
                    <p class="text-gray-600">Upload documents, PDFs, and websites. AI learns from your content to provide accurate answers.</p>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition text-center card-hover">
                    <div class="gradient-bg rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Live Chat & Analytics</h3>
                    <p class="text-gray-600">Real-time chat interface with human takeover. Track conversations, leads, and performance metrics.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Simple, Transparent Pricing</h2>
                <p class="text-xl text-gray-600 mt-4">Start for free, upgrade as you grow</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="p-8 text-center">
                        <h3 class="text-2xl font-bold text-gray-900">Free</h3>
                        <p class="text-4xl font-bold text-gray-900 mt-4">$0</p>
                        <p class="text-gray-500">per month</p>
                        <ul class="mt-6 space-y-3 text-left">
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> 1 Bot</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Basic AI Models</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Website Widget</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> 30 Days History</li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-8 block border-2 border-gray-300 text-gray-700 px-4 py-2 rounded-full hover:border-purple-600 hover:text-purple-600 transition">Get Started</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform scale-105 border-2 border-purple-500">
                    <div class="gradient-bg text-white text-center py-2">
                        <span class="text-sm font-semibold">MOST POPULAR</span>
                    </div>
                    <div class="p-8 text-center">
                        <h3 class="text-2xl font-bold text-gray-900">Pro</h3>
                        <p class="text-4xl font-bold text-gray-900 mt-4">$29</p>
                        <p class="text-gray-500">per month</p>
                        <ul class="mt-6 space-y-3 text-left">
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Up to 5 Bots</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Advanced AI Models</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Multi-channel Support</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Knowledge Base (RAG)</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Analytics & Reports</li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-8 block gradient-bg text-white px-4 py-2 rounded-full font-semibold hover:opacity-90 transition">Start Free Trial</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="p-8 text-center">
                        <h3 class="text-2xl font-bold text-gray-900">Business</h3>
                        <p class="text-4xl font-bold text-gray-900 mt-4">$99</p>
                        <p class="text-gray-500">per month</p>
                        <ul class="mt-6 space-y-3 text-left">
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Unlimited Bots</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Priority Support</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Custom AI Training</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> API Access</li>
                            <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> White-label Option</li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-8 block border-2 border-gray-300 text-gray-700 px-4 py-2 rounded-full hover:border-purple-600 hover:text-purple-600 transition">Contact Sales</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="gradient-bg py-20">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Transform Your Customer Support?</h2>
            <p class="text-white/80 text-lg mb-8">Join thousands of businesses already using AI chatbots to serve their customers.</p>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-purple-600 px-8 py-4 rounded-full font-semibold text-lg hover:shadow-xl transition transform hover:scale-105">
                Start Free Trial
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">&copy; {{ date('Y') }} SaaS AI Chatbot. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
