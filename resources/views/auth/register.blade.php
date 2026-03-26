<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - SaaS AI Chatbot</title>

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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full animate-fade-in-up">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-xl mb-4">
                <i class="fas fa-robot text-4xl gradient-text"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">Create Account</h1>
            <p class="text-white/80 mt-2">Start building AI chatbots today</p>
        </div>

        <!-- Register Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <form method="POST" action="{{ route('register') }}" x-data="registerForm()" x-init="init()">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-blue-500"></i>Full Name
                        </label>
                        <input type="text" name="name" x-model="form.name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="John Doe">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>Email Address
                        </label>
                        <input type="email" name="email" x-model="form.email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="you@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-blue-500"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" name="password" x-model="form.password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                   placeholder="••••••••">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <div x-show="form.password.length >= 8" class="text-green-500" title="Password strength">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 flex gap-2 text-xs">
                            <span :class="{'text-green-600': form.password.length >= 8}" class="text-gray-500">
                                <i class="fas fa-circle" :class="{'text-green-500': form.password.length >= 8}"></i> 8+ chars
                            </span>
                            <span :class="{'text-green-600': /[0-9]/.test(form.password)}" class="text-gray-500">
                                <i class="fas fa-circle" :class="{'text-green-500': /[0-9]/.test(form.password)}"></i> Number
                            </span>
                            <span :class="{'text-green-600': /[A-Z]/.test(form.password) && /[a-z]/.test(form.password)}" class="text-gray-500">
                                <i class="fas fa-circle" :class="{'text-green-500': /[A-Z]/.test(form.password) && /[a-z]/.test(form.password)}"></i> Mixed case
                            </span>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-blue-500"></i>Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" x-model="form.password_confirmation" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="••••••••">
                        <div x-show="form.password && form.password_confirmation && form.password !== form.password_confirmation"
                             class="mt-1 text-xs text-red-600">
                            <i class="fas fa-exclamation-circle"></i> Passwords do not match
                        </div>
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" name="terms" id="terms" x-model="form.terms" required
                               class="mt-1 w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <label for="terms" class="ml-2 text-sm text-gray-600">
                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-700">Terms of Service</a> and
                            <a href="#" class="text-blue-600 hover:text-blue-700">Privacy Policy</a>
                        </label>
                    </div>
                    @error('terms')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        :disabled="!isFormValid"
                        class="mt-6 w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition transform hover:scale-105 duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Sign in</a>
                </p>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-3 text-center text-xs text-gray-500">
                    <div>
                        <i class="fas fa-robot text-blue-500 block mb-1"></i>
                        <span>1 Free Bot</span>
                    </div>
                    <div>
                        <i class="fas fa-comments text-green-500 block mb-1"></i>
                        <span>Multi-channel</span>
                    </div>
                    <div>
                        <i class="fas fa-brain text-purple-500 block mb-1"></i>
                        <span>AI Knowledge Base</span>
                    </div>
                    <div>
                        <i class="fas fa-chart-line text-orange-500 block mb-1"></i>
                        <span>Analytics</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
        },
        init() {
            // Any initialization
        }
    }
}
</script>
</body>
</html>
