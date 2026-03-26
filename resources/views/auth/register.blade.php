<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" x-init="initTheme()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Join Us - SaaS AI Chatbot</title>

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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        @keyframes gentleFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-gentle {
            animation: gentleFade 0.6s ease-out;
        }
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
        .btn-warm {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #2c2418;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 1rem;
            transition: all 0.3s ease;
        }
        .btn-warm:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
        }
        .dark .btn-warm {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #fef3c7;
        }
        .card-warm {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .dark .card-warm {
            background: #1f2937;
        }
    </style>
</head>
<body :class="{ 'dark': isDarkMode }" class="min-h-screen flex items-center justify-center py-12 px-4 transition-colors duration-300">

    <button @click="toggleTheme()"
            class="fixed top-6 right-6 p-3 rounded-2xl bg-white dark:bg-gray-800 shadow-lg border border-amber-100 dark:border-gray-700 transition-all hover:scale-110 active:scale-95 z-50">
        <i x-show="!isDarkMode" class="fas fa-moon text-amber-600"></i>
        <i x-show="isDarkMode" class="fas fa-sun text-amber-400"></i>
    </button>

    <div class="max-w-md w-full animate-gentle">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 gradient-warm rounded-2xl shadow-xl mb-4">
                <i class="fas fa-robot text-amber-900 text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">Join Our Family</h1>
            <p class="text-amber-600 dark:text-amber-400 mt-2">Start your AI journey with us 🚀</p>
        </div>

        <!-- Register Card -->
        <div class="card-warm p-8">
            <form method="POST" action="{{ route('register') }}" x-data="registerForm()">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-user mr-2 text-amber-500"></i>What should we call you?
                        </label>
                        <input type="text" name="name" x-model="form.name" required
                               class="input-warm w-full"
                               placeholder="e.g., Sarah Johnson">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-envelope mr-2 text-amber-500"></i>Your Email
                        </label>
                        <input type="email" name="email" x-model="form.email" required
                               class="input-warm w-full"
                               placeholder="hello@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-lock mr-2 text-amber-500"></i>Create Password
                        </label>
                        <input type="password" name="password" x-model="form.password" required
                               class="input-warm w-full"
                               placeholder="Make it secure">
                        <div class="mt-2 flex gap-2 text-xs">
                            <span :class="{'text-green-600': form.password.length >= 8}" class="text-amber-500">
                                <i class="fas fa-circle" :class="{'text-green-500': form.password.length >= 8}"></i> 8+ chars
                            </span>
                            <span :class="{'text-green-600': /[0-9]/.test(form.password)}" class="text-amber-500">
                                <i class="fas fa-circle" :class="{'text-green-500': /[0-9]/.test(form.password)}"></i> Number
                            </span>
                            <span :class="{'text-green-600': /[A-Z]/.test(form.password) && /[a-z]/.test(form.password)}" class="text-amber-500">
                                <i class="fas fa-circle" :class="{'text-green-500': /[A-Z]/.test(form.password) && /[a-z]/.test(form.password)}"></i> Mixed case
                            </span>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-lock mr-2 text-amber-500"></i>Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" x-model="form.password_confirmation" required
                               class="input-warm w-full"
                               placeholder="Type it again">
                        <div x-show="form.password && form.password_confirmation && form.password !== form.password_confirmation"
                             class="mt-1 text-xs text-red-500">
                            <i class="fas fa-exclamation-circle"></i> Passwords don't match
                        </div>
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" name="terms" id="terms" x-model="form.terms" required
                               class="mt-1 w-4 h-4 text-amber-500 rounded focus:ring-amber-500">
                        <label for="terms" class="ml-2 text-sm text-amber-600 dark:text-amber-400">
                            I agree to the <a href="#" class="text-amber-600 hover:text-amber-700">Terms of Service</a> and
                            <a href="#" class="text-amber-600 hover:text-amber-700">Privacy Policy</a>
                        </label>
                    </div>
                    @error('terms')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        :disabled="!isFormValid"
                        class="mt-6 btn-warm w-full disabled:opacity-50 disabled:cursor-not-allowed">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-amber-600 dark:text-amber-400">
                    Already with us?
                    <a href="{{ route('login') }}" class="text-amber-600 hover:text-amber-700 font-semibold">Sign in</a>
                </p>
            </div>

            <!-- Features Preview -->
            <div class="mt-6 pt-6 border-t border-amber-100 dark:border-gray-700">
                <div class="grid grid-cols-2 gap-3 text-center text-xs text-amber-500">
                    <div>
                        <i class="fas fa-robot text-amber-500 block mb-1"></i>
                        <span>1 Free Bot</span>
                    </div>
                    <div>
                        <i class="fas fa-heart text-amber-500 block mb-1"></i>
                        <span>Multi-channel</span>
                    </div>
                    <div>
                        <i class="fas fa-brain text-amber-500 block mb-1"></i>
                        <span>AI Knowledge</span>
                    </div>
                    <div>
                        <i class="fas fa-chart-line text-amber-500 block mb-1"></i>
                        <span>Analytics</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-amber-400 text-sm mt-8">
            &copy; {{ date('Y') }} SaaS AI Chatbot. Welcome to the family! 💝
        </p>
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

        function registerForm() {
            return {
                form: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    terms: false
                },
                get isFormValid() {
                    return this.form.name &&
                           this.form.email &&
                           this.form.password &&
                           this.form.password === this.form.password_confirmation &&
                           this.form.password.length >= 8 &&
                           /[0-9]/.test(this.form.password) &&
                           /[A-Z]/.test(this.form.password) &&
                           /[a-z]/.test(this.form.password) &&
                           this.form.terms;
                }
            }
        }
    </script>
</body>
</html>
