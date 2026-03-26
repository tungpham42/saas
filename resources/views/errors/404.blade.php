<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" x-init="initTheme()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lost? Let's Find Your Way - SaaS AI Chatbot</title>

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
        body {
            background: linear-gradient(135deg, #fef9e7 0%, #fff5e6 100%);
        }
        .dark body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }
        .gradient-warm {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
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
    </style>
</head>
<body :class="{ 'dark': isDarkMode }" class="min-h-screen flex items-center justify-center p-4 transition-colors duration-300">
    <div class="text-center max-w-md">
        <div class="text-8xl font-bold mb-6 float-animation">
            <span class="gradient-warm bg-clip-text text-transparent bg-gradient-to-r from-amber-500 to-orange-500">404</span>
        </div>

        <div class="card-warm p-8 rounded-2xl">
            <div class="w-24 h-24 gradient-warm rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-compass text-amber-900 text-4xl"></i>
            </div>

            <h1 class="text-2xl font-bold text-amber-800 dark:text-amber-200 mb-2">Oops! You're Lost 🌟</h1>
            <p class="text-amber-600 dark:text-amber-400 mb-6">The page you're looking for seems to have wandered off. Let's get you back home!</p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/') }}" class="btn-warm px-6 py-3 rounded-full inline-flex items-center justify-center gap-2">
                    <i class="fas fa-home"></i>
                    <span>Go Home</span>
                </a>
                <button onclick="history.back()" class="btn-outline-warm px-6 py-3 rounded-full inline-flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Go Back</span>
                </button>
            </div>

            <div class="mt-8 pt-6 border-t border-amber-100 dark:border-gray-700">
                <div class="flex items-center justify-center gap-2 text-amber-400">
                    <i class="fas fa-heart"></i>
                    <span class="text-sm">Our AI is here to help if you need anything!</span>
                    <i class="fas fa-robot"></i>
                </div>
            </div>
        </div>

        <!-- Theme Toggle -->
        <div class="mt-6">
            <button @click="toggleTheme()" class="text-amber-500 hover:text-amber-600 inline-flex items-center gap-2 text-sm">
                <i x-show="!isDarkMode" class="fas fa-moon"></i>
                <i x-show="isDarkMode" class="fas fa-sun"></i>
                <span x-show="!isDarkMode">Switch to Dark Mode</span>
                <span x-show="isDarkMode">Switch to Light Mode</span>
            </button>
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
</body>
</html>
