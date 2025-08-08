<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Ukumbi') }}</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="Admin dashboard for managing venues, services, bookings, and users on {{ config('app.name', 'Ukumbi') }} platform">
        <meta name="robots" content="noindex, nofollow">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Chart.js for admin analytics -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="flex h-screen bg-gray-100">
            <!-- Sidebar Overlay (Mobile Only) -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 flex z-40 lg:hidden">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
            </div>

            <!-- Sidebar -->
            <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:flex lg:flex-col"
                 :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">
                
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-16 px-4 bg-gradient-to-r from-blue-900 to-blue-800">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-blue-900 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold text-lg">{{ config('app.name', 'Ukumbi') }}</div>
                            <div class="text-blue-200 text-xs">Admin Panel</div>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden text-blue-200 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700' : '' }}">
                        <i class="fas fa-chart-line w-5 mr-3"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <!-- Venues Management -->
                    <div x-data="{ open: {{ request()->routeIs('admin.venues.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.venues.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                            <div class="flex items-center">
                                <i class="fas fa-building w-5 mr-3"></i>
                                <span class="font-medium">Venues</span>
                            </div>
                            <i class="fas fa-chevron-down transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                            <a href="{{ route('venue.management') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.venues.index') ? 'bg-blue-50 text-blue-700' : '' }}">
                                All Venues
                            </a>
                            <!-- <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.venues.pending') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Pending Approval
                                <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">3</span>
                            </a> -->
                            <a href="{{ route('categories.management') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.venues.categories') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Categories
                            </a>
                        </div>
                    </div>

                    <!-- Services Management -->
                    <div x-data="{ open: {{ request()->routeIs('admin.services.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.services.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                            <div class="flex items-center">
                                <i class="fas fa-concierge-bell w-5 mr-3"></i>
                                <span class="font-medium">Services</span>
                            </div>
                            <i class="fas fa-chevron-down transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                            <a href="{{ route('services.management') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.services.index') ? 'bg-blue-50 text-blue-700' : '' }}">
                                All Services
                            </a>
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.services.pending') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Pending Approval
                                <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">5</span>
                            </a>
                            <!-- <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.services.categories') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Categories
                            </a> -->
                        </div>
                    </div>

                    <!-- Bookings Management -->
                    <div x-data="{ open: {{ request()->routeIs('admin.bookings.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.bookings.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-check w-5 mr-3"></i>
                                <span class="font-medium">Bookings</span>
                            </div>
                            <i class="fas fa-chevron-down transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                            <a href="{{ route('booking.management') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.bookings.index') ? 'bg-blue-50 text-blue-700' : '' }}">
                                All Bookings
                            </a>
                            <a href="{{ route('pending.booking') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.bookings.pending') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Pending Confirmation
                                <span class="ml-auto bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">12</span>
                            </a>
                            <a href="{{ route('payment.issues') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.bookings.payments') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Payment Issues
                            </a>
                        </div>
                    </div>

                    <!-- Users Management -->
                    <div x-data="{ open: {{ request()->routeIs('admin.users.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                            <div class="flex items-center">
                                <i class="fas fa-users w-5 mr-3"></i>
                                <span class="font-medium">Users</span>
                            </div>
                            <i class="fas fa-chevron-down transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                            <a href="{{ route('user.management') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.users.index') ? 'bg-blue-50 text-blue-700' : '' }}">
                                All Users
                            </a>
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.users.clients') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Clients
                            </a>
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.users.vendors') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Vendors
                            </a>
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.users.admins') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Administrators
                            </a>
                        </div>
                    </div>

                    <!-- Reviews & Reports -->
                    <a href="{{ route('review.manage') }}" 
                       class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.reviews.*') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700' : '' }}">
                        <i class="fas fa-star w-5 mr-3"></i>
                        <span class="font-medium">Reviews</span>
                        <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">2</span>
                    </a>

                    <!-- Financial Reports -->
                    <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar w-5 mr-3"></i>
                                <span class="font-medium">Reports</span>
                            </div>
                            <i class="fas fa-chevron-down transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.reports.revenue') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Revenue Reports
                            </a>
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.reports.analytics') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Platform Analytics
                            </a>
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.reports.vendors') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Vendor Performance
                            </a>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-4"></div>

                    <!-- Settings -->
                    <div x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                            <div class="flex items-center">
                                <i class="fas fa-cog w-5 mr-3"></i>
                                <span class="font-medium">Settings</span>
                            </div>
                            <i class="fas fa-chevron-down transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.settings.general') ? 'bg-blue-50 text-blue-700' : '' }}">
                                General Settings
                            </a>
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.settings.payments') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Payment Settings
                            </a>
                            <a href="" 
                               class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('admin.settings.notifications') ? 'bg-blue-50 text-blue-700' : '' }}">
                                Notifications
                            </a>
                        </div>
                    </div>

                    <!-- System Logs -->
                    <a href="" 
                       class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.logs.*') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700' : '' }}">
                        <i class="fas fa-file-alt w-5 mr-3"></i>
                        <span class="font-medium">System Logs</span>
                    </a>
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex flex-col flex-1 lg:ml-0">
                <!-- Top Navigation Bar -->
                <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                    <div class="flex items-center justify-between px-4 py-4">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <!-- Page Title -->
                        <div class="flex items-center">
                            <h1 class="text-2xl font-semibold text-gray-900">
                                @yield('title', 'Dashboard')
                            </h1>
                            @if(isset($breadcrumbs))
                                <nav class="ml-4 text-sm text-gray-500">
                                    @foreach($breadcrumbs as $crumb)
                                        @if(!$loop->last)
                                            <a href="{{ $crumb['url'] }}" class="hover:text-gray-700">{{ $crumb['name'] }}</a>
                                            <span class="mx-2">/</span>
                                        @else
                                            <span class="text-gray-900 font-medium">{{ $crumb['name'] }}</span>
                                        @endif
                                    @endforeach
                                </nav>
                            @endif
                        </div>

                        <!-- Top Navigation Actions -->
                        <div class="flex items-center space-x-4">
                            <!-- Quick Actions Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200">
                                    <i class="fas fa-plus-circle text-lg mr-1"></i>
                                    <span class="hidden sm:block">Quick Add</span>
                                    <i class="fas fa-chevron-down text-sm ml-1"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                    <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-building mr-3 text-gray-400"></i>
                                        Add Venue
                                    </a>
                                    <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-concierge-bell mr-3 text-gray-400"></i>
                                        Add Service
                                    </a>
                                    <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-user-plus mr-3 text-gray-400"></i>
                                        Add User
                                    </a>
                                </div>
                            </div>

                            <!-- Notifications -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                                    <i class="fas fa-bell text-lg"></i>
                                    <!-- Notification Badge -->
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        8
                                    </span>
                                </button>
                                <div x-show="open" @click.away="open = false" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                    <div class="px-4 py-2 border-b border-gray-200">
                                        <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <!-- Sample notifications -->
                                        <a href="#" class="flex items-start px-4 py-3 hover:bg-gray-50 transition-colors duration-200">
                                            <div class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-900">New venue pending approval</p>
                                                <p class="text-xs text-gray-500">2 minutes ago</p>
                                            </div>
                                        </a>
                                        <a href="#" class="flex items-start px-4 py-3 hover:bg-gray-50 transition-colors duration-200">
                                            <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-900">Payment dispute reported</p>
                                                <p class="text-xs text-gray-500">1 hour ago</p>
                                            </div>
                                        </a>
                                        <a href="#" class="flex items-start px-4 py-3 hover:bg-gray-50 transition-colors duration-200">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-900">New user registered</p>
                                                <p class="text-xs text-gray-500">3 hours ago</p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="border-t border-gray-200 px-4 py-2">
                                        <a href="" class="text-sm text-blue-600 hover:text-blue-800">
                                            View all notifications
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- User Profile -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="hidden md:block font-medium">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                    
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>

                                    <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-user mr-3 text-gray-400"></i>
                                        Profile Settings
                                    </a>

                                    <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-home mr-3 text-gray-400"></i>
                                        View Website
                                    </a>

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
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="transform translate-y-2 opacity-0"
                         x-transition:enter-end="transform translate-y-0 opacity-100"
                         class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 mx-4 mt-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                            <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="transform translate-y-2 opacity-0"
                         x-transition:enter-end="transform translate-y-0 opacity-100"
                         class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 mx-4 mt-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ session('error') }}</span>
                            <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 p-4 lg:p-6 overflow-y-auto">
                    @yield('content')
                    {{ $slot ?? '' }}
                </main>
            </div>
        </div>

        @stack('modals')
        @livewireScripts

        <!-- Alpine.js for interactive components -->
        
        @stack('scripts')
    </body>
</html>



<aside>
    
</aside>