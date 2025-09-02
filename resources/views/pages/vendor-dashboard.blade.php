@extends('layouts.app')

@section('title', 'Vendor Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Vendor Dashboard</h1>
            <p class="mt-2 text-gray-600">Manage your venues, services, and bookings</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Venues -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-building text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Venues</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ auth()->user()->venues()->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Services -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-concierge-bell text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Services</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ auth()->user()->services()->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Bookings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Bookings</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ auth()->user()->venues()->withCount(['bookings' => function($query) { 
                                $query->where('status', 'pending'); 
                            }])->get()->sum('bookings_count') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            TSh {{ number_format(auth()->user()->venues()->with('bookings')->get()->sum(function($venue) {
                                return $venue->bookings->where('status', 'confirmed')->sum('total_amount');
                            })) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('venue.management') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition-colors">
                <i class="fas fa-building text-2xl mb-2"></i>
                <p class="font-medium">Manage Venues</p>
            </a>
            
            <a href="{{ route('services.management') }}" class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition-colors">
                <i class="fas fa-concierge-bell text-2xl mb-2"></i>
                <p class="font-medium">Manage Services</p>
            </a>
            
            <a href="{{ route('booking.management') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white p-4 rounded-lg text-center transition-colors">
                <i class="fas fa-calendar-check text-2xl mb-2"></i>
                <p class="font-medium">View Bookings</p>
            </a>
            
            <a href="{{ route('admin.services.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition-colors">
                <i class="fas fa-plus text-2xl mb-2"></i>
                <p class="font-medium">Add Service</p>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Bookings</h3>
                </div>
                <div class="p-6">
                    @php
                        $recentBookings = auth()->user()->venues()->with(['bookings' => function($query) {
                            $query->with('user')->latest()->take(5);
                        }])->get()->flatMap->bookings->take(5);
                    @endphp
                    
                    @if($recentBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentBookings as $booking)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $booking->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->venue->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->event_date->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $booking->status_color }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        <p class="text-sm font-medium text-gray-900 mt-1">
                                            TSh {{ number_format($booking->total_amount) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No bookings yet</p>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Performance Overview</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Active Venues</span>
                            <span class="font-medium">{{ auth()->user()->venues()->where('status', 'active')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Active Services</span>
                            <span class="font-medium">{{ auth()->user()->services()->where('status', 'active')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">This Month's Bookings</span>
                            <span class="font-medium">
                                {{ auth()->user()->venues()->withCount(['bookings' => function($query) { 
                                    $query->whereMonth('created_at', now()->month); 
                                }])->get()->sum('bookings_count') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Average Rating</span>
                            <span class="font-medium flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                {{ number_format(auth()->user()->average_rating, 1) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

