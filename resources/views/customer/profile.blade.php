@extends('layouts.app')

@section('title', 'My Profile - PaSSSna Restaurant')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold text-gray-800 mb-8">My Profile</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <div class="w-32 h-32 bg-gradient-to-r from-primary-red to-primary-yellow rounded-full flex items-center justify-center mx-auto mb-4">
                    @if($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover">
                    @else
                    <span class="text-4xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                    @endif
                </div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
                <div class="mt-4">
                    <span class="bg-primary-yellow text-gray-800 px-4 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-crown mr-1"></i>{{ ucfirst($user->role ?? 'customer') }}
                    </span>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('customer.orders') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300">
                            <i class="fas fa-shopping-bag w-6"></i>My Orders
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.reservations') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300">
                            <i class="fas fa-calendar-alt w-6"></i>My Reservations
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.loyalty') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300">
                            <i class="fas fa-star w-6"></i>Loyalty Points
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Account Information</h3>

                <form action="{{ route('customer.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-gray-700 font-semibold mb-2">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                            @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 font-semibold mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                            @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-gray-700 font-semibold mb-2">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                            @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="dob" class="block text-gray-700 font-semibold mb-2">Date of Birth</label>
                            <input type="date" id="dob" name="dob" value="{{ old('dob', $user->date_of_birth) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                        </div>
                    </div>

                    <!-- Address Section -->
                    <h4 class="text-lg font-bold text-gray-800 mb-4">Address</h4>
                    <div class="mb-6">
                        <label for="address" class="block text-gray-700 font-semibold mb-2">Street Address</label>
                        <textarea id="address" name="address" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">{{ old('address', $user->address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label for="city" class="block text-gray-700 font-semibold mb-2">City</label>
                            <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                        </div>
                        <div>
                            <label for="state" class="block text-gray-700 font-semibold mb-2">State/Province</label>
                            <input type="text" id="state" name="state" value="{{ old('state', $user->state) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                        </div>
                        <div>
                            <label for="zip" class="block text-gray-700 font-semibold mb-2">ZIP Code</label>
                            <input type="text" id="zip" name="zip" value="{{ old('zip', $user->zip_code) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                        </div>
                    </div>

                    <!-- Notification Preferences -->
                    <h4 class="text-lg font-bold text-gray-800 mb-4">Notification Preferences</h4>
                    <div class="flex flex-wrap gap-4 mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="email_notifications" value="1" {{ $user->email_notifications ? 'checked' : '' }}
                                   class="mr-2 w-5 h-5 text-primary-red border-gray-300 rounded focus:ring-primary-red">
                            <span class="text-gray-700">Email Notifications</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="sms_notifications" value="1" {{ $user->sms_notifications ? 'checked' : '' }}
                                   class="mr-2 w-5 h-5 text-primary-red border-gray-300 rounded focus:ring-primary-red">
                            <span class="text-gray-700">SMS Notifications</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="promotional_emails" value="1" {{ $user->promotional_emails ? 'checked' : '' }}
                                   class="mr-2 w-5 h-5 text-primary-red border-gray-300 rounded focus:ring-primary-red">
                            <span class="text-gray-700">Promotional Emails</span>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-primary-red to-primary-yellow text-white px-8 py-3 rounded-lg font-semibold hover:opacity-90 transition duration-300">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Change -->
            <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Change Password</h3>

                <form action="{{ route('customer.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="current_password" class="block text-gray-700 font-semibold mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                        @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700 font-semibold mb-2">New Password</label>
                        <input type="password" id="new_password" name="new_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                        @error('new_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="new_password_confirmation" class="block text-gray-700 font-semibold mb-2">Confirm New Password</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-red">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-gray-800 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-900 transition duration-300">
                            <i class="fas fa-lock mr-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-lg p-6 mt-6 border border-red-200">
                <h3 class="text-2xl font-bold text-red-600 mb-6">Danger Zone</h3>
                <p class="text-gray-600 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                <button onclick="confirmDeleteAccount()" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-300">
                    <i class="fas fa-trash-alt mr-2"></i>Delete Account
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDeleteAccount() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone. All your data will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete my account!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit delete form
                document.getElementById('deleteAccountForm').submit();
            }
        });
    }
</script>

<form id="deleteAccountForm" action="{{ route('customer.profile.delete') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

