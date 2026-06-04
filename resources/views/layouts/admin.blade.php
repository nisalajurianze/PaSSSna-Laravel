<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PaSSSna Admin</title>

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
                        'admin-dark': '#1F2937',
                        'admin-light': '#F3F4F6',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transition-transform duration-300 lg:static lg:translate-x-0">
            <div class="flex h-16 items-center justify-between px-6 border-b border-gray-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">P</span>
                    </div>
                    <span class="text-lg font-semibold">PaSSSna</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-home w-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.orders*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span>Orders</span>
                        @php $pendingOrders = \App\Models\Order::where('status', 'pending')->count(); @endphp
                        @if($pendingOrders > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingOrders }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.reservations.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.reservations*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-calendar-alt w-5"></i>
                        <span>Reservations</span>
                    </a>

                    <a href="{{ route('admin.menu.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.menu*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-utensils w-5"></i>
                        <span>Menu</span>
                    </a>

                    <a href="{{ route('admin.inventory.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.inventory*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-boxes w-5"></i>
                        <span>Inventory</span>
                    </a>

                    <a href="{{ route('admin.tables.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.tables*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-chair w-5"></i>
                        <span>Tables</span>
                    </a>

                    <a href="{{ route('admin.dining.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.dining*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-concierge-bell w-5"></i>
                        <span>Dining</span>
                        @php $activeDining = \App\Models\DiningSession::where('status', 'active')->count(); @endphp
                        @if($activeDining > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $activeDining }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.staff*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-users w-5"></i>
                        <span>Staff</span>
                    </a>

                    <a href="{{ route('admin.promotions.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.promotions*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-tag w-5"></i>
                        <span>Promotions</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.reports*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Reports</span>
                    </a>

                    <a href="{{ route('admin.contact.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('admin.contact*') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-envelope w-5"></i>
                        <span>Messages</span>
                    </a>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-800">
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white">
                        <i class="fas fa-external-link-alt w-5"></i>
                        <span>View Site</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button type="button" onclick="adminGoBack()" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:text-gray-800 hover:border-gray-300 transition">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">@yield('header')</h1>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User Menu -->
                    <div class="flex items-center gap-3" x-data="{ open: false }">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function adminGoBack() {
            if (window.history.length > 1) {
                window.history.back();
                return;
            }
            window.location.href = "{{ route('admin.dashboard') }}";
        }
    </script>
    @stack('scripts')
</body>
</html>

