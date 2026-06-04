@extends('layouts.app')

@section('title', 'Login - PaSSSna Restaurant')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-yellow-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 animate-fade-in">
        <!-- Logo -->
        <div class="text-center">
            <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="mx-auto h-32 w-auto mb-4">
            <h2 class="text-3xl font-bold text-gray-900">Welcome to PaSSSna</h2>
            <p class="mt-2 text-sm text-gray-600">
                Please sign in to your account
            </p>
        </div>

        <!-- Login Form -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg sm:px-10">
            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email Address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm @error('email') border-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm @error('password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="eyeIcon" class="far fa-eye text-gray-400"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                               class="h-4 w-4 text-primary-red focus:ring-primary-red border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-primary-red hover:text-red-700">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-red transition duration-300 transform hover:scale-105">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt"></i>
                        </span>
                        Sign in
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                    </div>
                </div>
            </div>

            <!-- Guest Login -->
            <div class="mt-6">
                <a href="{{ route('register') }}"
                   class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-red transition duration-300">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create new account
                </a>
            </div>

            <!-- Demo Credentials -->
            <div class="mt-6 p-4 bg-gray-50 rounded-md">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Demo Credentials:</h4>
                <div class="space-y-1 text-sm text-gray-600">
                    <div>Admin: admin.passsna@gmail.com / PaSSSna_log</div>
                    <div>Customer: customer@example.com / password</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection

