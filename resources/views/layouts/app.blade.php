<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PaSSSna Restaurant</title>

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-red': '#DC2626',
                        'primary-yellow': '#FBBF24',
                        'navy-blue': '#1E3A8A',
                        'light-red': '#FEE2E2',
                        'light-yellow': '#FEF3C7',
                        'paper': '#F3EEE7',
                        'surface': '#F8F3ED',
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                        'bounce-slow': 'bounce 2s infinite',
                        'pulse-slow': 'pulse 3s infinite',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .gradient-bg { background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%); }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .menu-item-hover:hover .menu-img { transform: scale(1.05); }
        .menu-item-hover .menu-img { transition: transform 0.3s ease; }
        :root {
            --paper: #F3EEE7;
            --surface: #F8F3ED;
            --stroke: #E7DDD2;
        }
        .theme-sand { background-color: var(--paper); }
        .theme-sand .bg-white { background-color: var(--surface) !important; }
        .theme-sand .border-gray-200,
        .theme-sand .border-gray-100 { border-color: var(--stroke) !important; }
    </style>

    @yield('styles')
</head>
<body class="font-sans bg-paper text-gray-800 theme-sand {{ ($kiosk ?? false) ? 'pt-0' : 'pt-32' }}">
    @php
        $kiosk = $kiosk ?? false;
    @endphp
    <!-- Navigation -->
    @unless($kiosk)
    <nav class="bg-gradient-to-r from-[#E2D1C2] via-[#EADCCD] to-[#F0E5D8] fixed top-0 left-0 right-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="h-12 w-auto">
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="{{ route('menu') }}" class="nav-link {{ request()->routeIs('menu*') ? 'active' : '' }}">
                        <i class="fas fa-utensils mr-2"></i>Menu
                    </a>
                    <a href="{{ route('reservation.create') }}" class="nav-link {{ request()->routeIs('reservation*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt mr-2"></i>Reservation
                    </a>
                    <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                        <i class="fas fa-phone-alt mr-2"></i>Contact
                    </a>

                    @auth
                        <a href="{{ route('customer.dashboard') }}" class="nav-link">
                            <i class="fas fa-user-circle mr-2"></i>Dashboard
                        </a>
                    @endauth
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications Bell -->
                    @auth
                    @php
                        $unreadNotifications = \App\Models\Notification::where('user_id', Auth::id())->unread()->count();
                    @endphp
                    <a href="{{ route('customer.dashboard') }}#notifications" class="relative group" id="notificationBell">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-primary-red transition duration-300">
                            <i class="fas fa-bell text-gray-700 group-hover:text-white text-lg transition duration-300"></i>
                        </div>
                        <span id="notificationBadge" class="absolute -top-1 -right-1 bg-primary-red text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse-slow {{ $unreadNotifications > 0 ? '' : 'hidden' }}">
                            {{ $unreadNotifications > 9 ? '9+' : ($unreadNotifications > 0 ? $unreadNotifications : '0') }}
                        </span>
                    </a>
                    @endauth

                    <!-- Cart -->
                    <a href="{{ route('cart') }}" class="relative group">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-primary-red transition duration-300">
                            <i class="fas fa-shopping-cart text-gray-700 group-hover:text-white text-lg transition duration-300"></i>
                        </div>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="absolute -top-1 -right-1 bg-primary-red text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse-slow">
                                {{ count(session('cart')) }}
                            </span>
                        @endif
                    </a>

                    <!-- Auth Buttons -->
                    @auth
                        <div class="relative group pb-2">
                            <button class="flex items-center space-x-2 bg-gray-100 px-4 py-2 rounded-lg hover:bg-gray-200 transition duration-300">
                                <i class="fas fa-user text-gray-700"></i>
                                <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="absolute right-0 top-full mt-2 w-48 bg-surface rounded-lg shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible hover:opacity-100 hover:visible transition-all duration-200 z-50">
                                <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('customer.orders') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-receipt mr-2"></i>My Orders
                                </a>
                                <a href="{{ route('customer.profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-edit mr-2"></i>Profile
                                </a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="bg-primary-yellow text-gray-800 px-4 py-2 rounded-lg font-medium hover:bg-yellow-500 transition duration-300 transform hover:scale-105 shadow-md">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuButton" class="md:hidden text-gray-700">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="md:hidden hidden py-4 border-t animate-slide-down">
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('home') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300 py-2">
                        <i class="fas fa-home w-6 mr-3"></i>Home
                    </a>
                    <a href="{{ route('menu') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300 py-2">
                        <i class="fas fa-utensils w-6 mr-3"></i>Menu
                    </a>
                    <a href="{{ route('reservation.create') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300 py-2">
                        <i class="fas fa-calendar-alt w-6 mr-3"></i>Reservation
                    </a>
                    <a href="{{ route('contact') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300 py-2">
                        <i class="fas fa-phone-alt w-6 mr-3"></i>Contact
                    </a>
                    @auth
                        <hr>
                        <a href="{{ route('customer.dashboard') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300 py-2">
                            <i class="fas fa-tachometer-alt w-6 mr-3"></i>Dashboard
                        </a>
                        <a href="{{ route('customer.orders') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300 py-2">
                            <i class="fas fa-receipt w-6 mr-3"></i>My Orders
                        </a>
                        <a href="{{ route('customer.profile') }}" class="flex items-center text-gray-700 hover:text-primary-red transition duration-300 py-2">
                            <i class="fas fa-user-edit w-6 mr-3"></i>Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full text-red-600 hover:text-red-700 py-2">
                                <i class="fas fa-sign-out-alt w-6 mr-3"></i>Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    @endunless

    <!-- Main Content -->
    <main class="min-h-screen -mt-px">
        @yield('content')
    </main>

    <!-- Footer -->
    @unless($kiosk)
    <footer class="bg-navy-blue text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Restaurant Info -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="h-16 w-auto">
                    </div>
                    <p class="text-gray-300 mb-4">Experience culinary excellence with authentic flavors and exceptional service.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary-yellow transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary-yellow transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary-yellow transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary-yellow transition duration-300">
                            <i class="fab fa-tripadvisor"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('menu') }}" class="text-gray-300 hover:text-primary-yellow transition duration-300">Menu</a></li>
                        <li><a href="{{ route('reservation.create') }}" class="text-gray-300 hover:text-primary-yellow transition duration-300">Reservation</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-300 hover:text-primary-yellow transition duration-300">Contact Us</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-yellow transition duration-300">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Info</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary-yellow"></i>
                            <span>123 Gourmet Street, Food City, FC 12345</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-primary-yellow"></i>
                            <span>+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-primary-yellow"></i>
                            <span>info@passsna.com</span>
                        </li>
                    </ul>
                </div>

                <!-- Opening Hours -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Opening Hours</h3>
                    <ul class="space-y-2">
                        <li class="flex justify-between">
                            <span>Monday - Thursday</span>
                            <span class="text-primary-yellow">11 AM - 10 PM</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Friday - Saturday</span>
                            <span class="text-primary-yellow">11 AM - 11 PM</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Sunday</span>
                            <span class="text-primary-yellow">12 PM - 9 PM</span>
                        </li>
                    </ul>
                    <div class="mt-4 p-3 bg-gray-800 rounded-lg">
                        <p class="text-sm">Call for reservations: <a href="tel:+15551234567" class="text-primary-yellow font-semibold">(555) 123-4567</a></p>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; {{ date('Y') }} PaSSSna Restaurant. All rights reserved.</p>
        <p class="text-sm mt-2">Website designed with <i class="fas fa-heart text-red-400"></i> for food lovers</p>
    </div>
</div>
    </footer>
    @endunless

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mobile Menu Toggle
        document.getElementById('mobileMenuButton')?.addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert-auto-hide');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s ease-in-out';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);

            // Add active class to nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                if(link.classList.contains('active')) {
                    link.classList.add('text-primary-red', 'font-semibold');
                    link.classList.remove('text-gray-700');
                }
            });
        });

        // Toast notification function
        function showToast(message, type = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }

        // Check for messages from session
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('status'))
            showToast("{{ session('status') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        @if(session('warning'))
            showToast("{{ session('warning') }}", 'warning');
        @endif

        // Real-time notification polling for authenticated users
        let lastNotificationCount = {{ $unreadNotifications ?? 0 }};

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count > 9 ? '9+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.textContent = '0';
                    badge.classList.add('hidden');
                }
            }
        }

        function showNewNotificationToast(notification) {
            showToast(notification.title, 'info');
        }

        function checkNotifications() {
            fetch('{{ route("customer.notifications.check") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update badge count
                        updateNotificationBadge(data.unread_count);

                        // Check if there are new notifications (more than last check)
                        if (data.new_notifications > 0 && data.unread_count > lastNotificationCount) {
                            // Fetch the latest notification details
                            fetch('{{ route("customer.notifications.recent") }}')
                                .then(response => response.json())
                                .then(notifData => {
                                    if (notifData.success && notifData.notifications.length > 0) {
                                        const latestNotification = notifData.notifications[0];
                                        showNewNotificationToast(latestNotification);
                                    }
                                });
                        }
                        lastNotificationCount = data.unread_count;
                    }
                })
                .catch(error => console.log('Notification check failed:', error));
        }

        // Start polling every 3 seconds for authenticated users
        @auth
        document.addEventListener('DOMContentLoaded', function() {
            // Initial check after 3 seconds
            setTimeout(checkNotifications, 3000);
            // Then poll every 3 seconds
            setInterval(checkNotifications, 3000);

            // Real-time polling for menu, order, and reservation updates
            initRealTimeUpdates();
        });
        @endauth

        // Real-time update polling system
        function initRealTimeUpdates() {
            // Poll for menu updates (only on menu pages)
            @if(!request()->routeIs('checkout*'))
            setInterval(() => {
                fetch('/api/menu/check-updated')
                    .then(response => response.json())
                    .then(data => {
                        if (data.updated) {
                            // Trigger menu reload
                            if (typeof reloadMenu === 'function') {
                                reloadMenu();
                            }
                            showToast('Menu has been updated!', 'info');
                        }
                    })
                    .catch(() => {}); // Silent fail
            }, 5000);
            @endif

            // Poll for order status updates
            @if(request()->routeIs('customer.orders*') || request()->routeIs('order*'))
            setInterval(() => {
                fetch('/api/orders/check-updated')
                    .then(response => response.json())
                    .then(data => {
                        if (data.updated && data.orderId) {
                            if (typeof reloadOrders === 'function') {
                                reloadOrders();
                            }
                            showToast(`Order #${data.orderNumber} status: ${data.status}`, 'info');
                        }
                    })
                    .catch(() => {});
            }, 3000);
            @endif

            // Poll for reservation updates
            @if(request()->routeIs('reservation*'))
            setInterval(() => {
                fetch('/api/reservations/check-updated')
                    .then(response => response.json())
                    .then(data => {
                        if (data.updated) {
                            if (typeof reloadReservations === 'function') {
                                reloadReservations();
                            }
                            showToast(`Reservation status: ${data.status}`, 'info');
                        }
                    })
                    .catch(() => {});
            }, 5000);
            @endif
        }
    </script>

    @yield('scripts')
</body>
</html>

