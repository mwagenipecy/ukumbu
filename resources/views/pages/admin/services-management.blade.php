@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Services Management</h2>
                <p class="mt-1 text-sm text-gray-600">Manage all services on the platform</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.services.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Service
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-concierge-bell text-blue-600"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Total Services</div>
                        <div class="text-xl font-semibold text-gray-900">{{ \App\Models\Service::count() }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Active</div>
                        <div class="text-xl font-semibold text-gray-900">
                            {{ \App\Models\Service::where('status', 'active')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Pending</div>
                        <div class="text-xl font-semibold text-gray-900">
                            {{ \App\Models\Service::where('status', 'pending')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-pause-circle text-red-600"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Suspended</div>
                        <div class="text-xl font-semibold text-gray-900">
                            {{ \App\Models\Service::where('status', 'suspended')->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">All Services</h3>
            </div>
            <div class="p-6">
                @if(\App\Models\Service::count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach(\App\Models\Service::with(['user', 'category'])->latest()->take(6)->get() as $service)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <h4 class="font-medium text-gray-900">{{ $service->name }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : ($service->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ Str::limit($service->description, 80) }}</p>
                                <div class="text-sm text-gray-500">
                                    <p>Provider: {{ $service->user->name }}</p>
                                    <p>Category: {{ $service->category->name ?? 'Uncategorized' }}</p>
                                    <p class="font-medium text-blue-600">{{ $service->formatted_price }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(\App\Models\Service::count() > 6)
                        <div class="mt-6 text-center">
                            <p class="text-gray-500">Showing 6 of {{ \App\Models\Service::count() }} services</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-concierge-bell text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No services found</h3>
                        <p class="text-gray-500 mb-6">Get started by adding the first service to the platform.</p>
                        <a href="{{ route('admin.services.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Add First Service
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection