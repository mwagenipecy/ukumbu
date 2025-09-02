<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VenueLink') }} - Find Perfect Venues & Services in Tanzania</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="Discover and book amazing event venues and services across Tanzania. Find the perfect location for your wedding, party, corporate event, or celebration.">
        <meta name="keywords" content="Tanzania venues, event booking, wedding venues, party venues, corporate events, catering, photography, Tanzania events">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <!-- Additional Meta Tags -->
        <meta property="og:title" content="{{ config('app.name', 'VenueLink') }} - Find Perfect Venues & Services">
        <meta property="og:description" content="Discover and book amazing event venues and services across Tanzania">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <x-banner />

        <!-- Navigation Header -->
        <nav class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('welcome.client.page') }}" class="flex items-center space-x-2">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-900 to-blue-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-white text-lg"></i>
                            </div>
                            <div>
                                <div class="text-xl font-bold text-blue-900">{{ config('app.name', 'VenueLink') }}</div>
                                <div class="text-xs text-gray-500 -mt-1">Find. Book. Celebrate.</div>
                            </div>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('welcome.client.page') }}" class="text-gray-700 hover:text-blue-900 font-medium transition-colors duration-200 {{ request()->routeIs('home') ? 'text-blue-900 border-b-2 border-blue-900 pb-1' : '' }}">
                            <i class="fas fa-home mr-1"></i>
                            Home
                        </a>
                        
                        <a href="{{ route('venue.management') }}" class="text-gray-700 hover:text-blue-900 font-medium transition-colors duration-200 {{ request()->routeIs('venue.*') ? 'text-blue-900 border-b-2 border-blue-900 pb-1' : '' }}">
                            <i class="fas fa-building mr-1"></i>
                            Venues
                        </a>
                        
                        <a href="{{ route('services.management') }}" class="text-gray-700 hover:text-blue-900 font-medium transition-colors duration-200 {{ request()->routeIs('services.*') ? 'text-blue-900 border-b-2 border-blue-900 pb-1' : '' }}">
                            <i class="fas fa-concierge-bell mr-1"></i>
                            Services
                        </a>

                        @auth
                            <a href="{{ route('booking.management') }}" class="text-gray-700 hover:text-blue-900 font-medium transition-colors duration-200 {{ request()->routeIs('booking.*') ? 'text-blue-900 border-b-2 border-blue-900 pb-1' : '' }}">
                                <i class="fas fa-calendar-check mr-1"></i>
                                My Bookings
                            </a>

                            <a href="{{ route('user.dashboard') }}" class="text-gray-700 hover:text-blue-900 font-medium transition-colors duration-200 {{ request()->routeIs('user.dashboard') ? 'text-blue-900 border-b-2 border-blue-900 pb-1' : '' }}">
                                <i class="fas fa-heart mr-1"></i>
                                Dashboard
                            </a>
                        @endauth
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        @guest
                            <a href="{{ route('register') }}" class="text-gray-700 hover:text-blue-900 font-medium transition-colors duration-200">
                                Register
                            </a>
                            <a href="{{ route('login') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                Login
                            </a>
                        @else
                            <!-- Notifications -->
                            <div class="relative">
                                <button class="relative p-2 text-gray-700 hover:text-blue-900 transition-colors duration-200">
                                    <i class="fas fa-bell text-xl"></i>
                                    <!-- Notification Badge -->
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        3
                                    </span>
                                </button>
                            </div>

                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-blue-900 font-medium transition-colors duration-200">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" @click.away="open = false" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                    
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>

                                    <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-user mr-3 text-gray-400"></i>
                                        Profile Settings
                                    </a>

                                    <a href="{{ route('booking.management') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-calendar-check mr-3 text-gray-400"></i>
                                        My Bookings
                                    </a>

                                    <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-heart mr-3 text-gray-400"></i>
                                        My Dashboard
                                    </a>

                                    @if(Auth::user()->role === 'vendor')
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="{{ route('venue.management') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-building mr-3 text-gray-400"></i>
                                            My Venues
                                        </a>
                                        <a href="{{ route('services.management') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-concierge-bell mr-3 text-gray-400"></i>
                                            My Services
                                        </a>
                                    @endif
                                    
                                    @if(Auth::user()->role === 'admin')
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="{{ route('admin.calendar') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-calendar mr-3 text-gray-400"></i>
                                            Admin Calendar
                                        </a>
                                        <a href="{{ route('user.management') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-users mr-3 text-gray-400"></i>
                                            User Management
                                        </a>
                                    @endif

                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest

                        <!-- Mobile Menu Button -->
                        <button x-data="{ open: false }" @click="open = !open" class="md:hidden p-2 text-gray-700 hover:text-blue-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div x-data="{ mobileMenuOpen: false }" class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="absolute top-4 right-4 p-2 text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <div x-show="mobileMenuOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="mobileMenuOpen = false"
                         class="absolute top-16 left-0 right-0 bg-white shadow-lg border-t border-gray-200 z-50">
                        
                        <div class="py-2">
                            <a href="{{ route('welcome.client.page') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-900 border-r-4 border-blue-900' : '' }}">
                                <i class="fas fa-home mr-3"></i>
                                Home
                            </a>
                            
                            <a href="" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 {{ request()->routeIs('venues.*') ? 'bg-blue-50 text-blue-900 border-r-4 border-blue-900' : '' }}">
                                <i class="fas fa-building mr-3"></i>
                                Venues
                            </a>
                            
                            <a href="" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 {{ request()->routeIs('services.*') ? 'bg-blue-50 text-blue-900 border-r-4 border-blue-900' : '' }}">
                                <i class="fas fa-concierge-bell mr-3"></i>
                                Services
                            </a>

                            @auth
                                <div class="border-t border-gray-100 my-2"></div>
                                
                                <a href="" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-calendar-check mr-3"></i>
                                    My Bookings
                                </a>
                                
                                <a href="" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-heart mr-3"></i>
                                    Favorites
                                </a>

                                @if(Auth::user()->role === 'vendor')
                                    <a href="{{ route('vendor.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-store mr-3"></i>
                                        Vendor Dashboard
                                    </a>
                                @endif

                                <div class="border-t border-gray-100 my-2"></div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-3 text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-sign-out-alt mr-3"></i>
                                        Logout
                                    </button>
                                </form>
                            @else
                                <div class="border-t border-gray-100 my-2"></div>
                                
                                <a href="{{ route('login') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-sign-in-alt mr-3"></i>
                                    Login
                                </a>
                                
                                <a href="{{ route('register') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user-plus mr-3"></i>
                                    Register
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="min-h-screen">
            <!-- Flash Messages -->
            @if(session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="transform translate-x-full opacity-0"
                     x-transition:enter-end="transform translate-x-0 opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="transform translate-x-0 opacity-100"
                     x-transition:leave-end="transform translate-x-full opacity-0"
                     class="fixed top-20 right-4 z-50 max-w-sm bg-green-500 text-white rounded-lg shadow-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="ml-auto text-white hover:text-green-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="transform translate-x-full opacity-0"
                     x-transition:enter-end="transform translate-x-0 opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="transform translate-x-0 opacity-100"
                     x-transition:leave-end="transform translate-x-full opacity-0"
                     class="fixed top-20 right-4 z-50 max-w-sm bg-red-500 text-white rounded-lg shadow-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto text-white hover:text-red-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Company Info -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-yellow-400 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-blue-900 text-lg"></i>
                            </div>
                            <div>
                                <div class="text-xl font-bold">{{ config('app.name', 'Ukumbi') }}</div>
                                <div class="text-sm text-gray-400">Find. Book. Celebrate.</div>
                            </div>
                        </div>
                        <p class="text-gray-300 mb-4">
                            Tanzania's premier platform for discovering and booking amazing event venues and services. 
                            From intimate gatherings to grand celebrations, we help you find the perfect space and services 
                            for your special moments.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-200">
                                <i class="fab fa-facebook text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-200">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-200">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-200">
                                <i class="fab fa-linkedin text-xl"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="" class="text-gray-300 hover:text-yellow-400 transition-colors duration-200">Browse Venues</a></li>
                            <li><a href="" class="text-gray-300 hover:text-yellow-400 transition-colors duration-200">Find Services</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-200">How It Works</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-200">Become a Vendor</a></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Support</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-200">Help Center</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-200">Contact Us</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-200">Terms of Service</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors duration-200">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Ukumbi') }}. All rights reserved.
                    </p>
                    <div class="flex items-center space-x-4 mt-4 md:mt-0">
                        <span class="text-gray-400 text-sm">Made with</span>
                        <i class="fas fa-heart text-red-500"></i>
                        <span class="text-gray-400 text-sm">in Tanzania</span>
                    </div>
                </div>
            </div>
        </footer>

        @stack('modals')

        @livewireScripts
        
        {{-- Alpine.js is included with Livewire --}}
    </body>
</html>