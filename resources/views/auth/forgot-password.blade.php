@extends('layouts.app')

@section('title', 'Forgot Password - PaSSSna Restaurant')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-yellow-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 animate-fade-in">
        <!-- Logo -->
        <div class="text-center">
            <div class="mx-auto w-20 h-20 bg-gradient-to-r from-primary-red to-primary-yellow rounded-full flex items-center justify-center mb-4">
                <span class="text-white text-3xl font-bold">P</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Reset Your Password</h2>
            <p class="mt-2 text-sm text-gray-600">
                Enter your email to receive a password reset link
            </p>
        </div>

        <!-- Forgot Password Form -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg sm:px-10">
            @if (session('status'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('password.email') }}" method="POST">
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

                <!-- Submit -->
                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-red transition duration-300 transform hover:scale-105">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-paper-plane"></i>
                        </span>
                        Send Reset Link
                    </button>
                </div>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-primary-red hover:text-red-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to login
                </a>
            </div>

            <!-- Instructions -->
            <div class="mt-6 p-4 bg-gray-50 rounded-md">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Instructions:</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><i class="fas fa-info-circle text-primary-red mr-2"></i>Check your spam folder if you don't receive the email</li>
                    <li><i class="fas fa-info-circle text-primary-red mr-2"></i>The reset link will expire in 60 minutes</li>
                    <li><i class="fas fa-info-circle text-primary-red mr-2"></i>Contact support if you need assistance</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

