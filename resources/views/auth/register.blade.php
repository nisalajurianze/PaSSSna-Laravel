@extends('layouts.app')

@section('title', 'Register - PaSSSna Restaurant')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-yellow-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 animate-fade-in">
        <!-- Logo -->
        <div class="text-center">
            <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="mx-auto h-32 w-auto mb-4">
            <h2 class="text-3xl font-bold text-gray-900">Create Your Account</h2>
            <p class="mt-2 text-sm text-gray-600">
                Join PaSSSna for exclusive offers and faster ordering
            </p>
        </div>

        <!-- Register Form -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg sm:px-10">
            <form class="space-y-6" action="{{ route('register') }}" method="POST" id="registerForm">
                @csrf

                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Full Name
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required
                               value="{{ old('name') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm @error('name') border-red-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        Phone Number
                    </label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="tel" autocomplete="tel" required
                               value="{{ old('phone') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm @error('phone') border-red-500 @enderror">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">
                        Address
                    </label>
                    <div class="mt-1">
                        <textarea id="address" name="address" rows="2"
                                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    </div>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm @error('password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="eyeIconPassword" class="far fa-eye text-gray-400"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimum 8 characters with letters and numbers</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirm Password
                    </label>
                    <div class="mt-1 relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-red focus:border-primary-red sm:text-sm">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="eyeIconConfirm" class="far fa-eye text-gray-400"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms -->
                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required
                           class="h-4 w-4 text-primary-red focus:ring-primary-red border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        I agree to the <a href="#" class="text-primary-red hover:text-red-700">Terms of Service</a> and <a href="#" class="text-primary-red hover:text-red-700">Privacy Policy</a>
                    </label>
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-red transition duration-300 transform hover:scale-105">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus"></i>
                        </span>
                        Create Account
                    </button>
                </div>
            </form>

            <!-- Already have account -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-primary-red hover:text-red-700">
                        Sign in
                    </a>
                </p>
            </div>

            <!-- Benefits -->
            <div class="mt-6 p-4 bg-gradient-to-r from-yellow-50 to-red-50 rounded-md">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Benefits of registering:</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Faster checkout</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Order history tracking</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Exclusive offers & discounts</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Easy table reservations</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(fieldId === 'password' ? 'eyeIconPassword' : 'eyeIconConfirm');

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

    // Password strength indicator
    document.getElementById('password').addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthBar = document.getElementById('password-strength');

        if (!strengthBar) {
            const div = document.createElement('div');
            div.id = 'password-strength';
            div.className = 'mt-2';
            e.target.parentNode.appendChild(div);
        }

        const strength = checkPasswordStrength(password);
        const strengthBarElement = document.getElementById('password-strength');

        let color, text, width;
        switch(strength) {
            case 'weak':
                color = 'bg-red-500';
                text = 'Weak password';
                width = '33%';
                break;
            case 'medium':
                color = 'bg-yellow-500';
                text = 'Medium password';
                width = '66%';
                break;
            case 'strong':
                color = 'bg-green-500';
                text = 'Strong password';
                width = '100%';
                break;
            default:
                color = 'bg-gray-200';
                text = '';
                width = '0%';
        }

        strengthBarElement.innerHTML = `
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full ${color} transition-all duration-300" style="width: ${width}"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">${text}</p>
        `;
    });

    function checkPasswordStrength(password) {
        if (password.length < 6) return 'weak';
        if (password.length < 8) return 'medium';

        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

        let score = 0;
        if (hasUpperCase) score++;
        if (hasLowerCase) score++;
        if (hasNumbers) score++;
        if (hasSpecial) score++;

        if (score >= 3 && password.length >= 8) return 'strong';
        if (score >= 2 && password.length >= 6) return 'medium';
        return 'weak';
    }
</script>
@endsection

