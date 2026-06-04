@extends('layouts.app')

@section('title', 'Reset Password - PaSSSna Restaurant')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-primary-red to-primary-yellow rounded-full flex items-center justify-center mx-auto shadow-lg">
                <i class="fas fa-utensils text-white text-2xl"></i>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Reset Your Password</h2>
            <p class="mt-2 text-sm text-gray-600">Enter your new password below</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm"
                        value="{{ $email ?? old('email') }}" readonly>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input id="password" name="password" type="password" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm"
                        placeholder="Enter new password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm"
                        placeholder="Confirm new password">
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-primary-red to-primary-yellow hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-red transition duration-300">
                        <i class="fas fa-lock mr-2"></i>
                        Reset Password
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-primary-red hover:text-red-700 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

