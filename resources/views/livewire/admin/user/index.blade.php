<div>
<div>
    @section('title', 'User Management')

    <div class="space-y-6">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <p class="mt-1 text-sm text-gray-500">Manage clients, vendors, and administrators</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                @if(count($selectedUsers) > 0)
                    <button wire:click="toggleBulkModal" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-tasks mr-2"></i>
                        Bulk Actions ({{ count($selectedUsers) }})
                    </button>
                @endif
                <button wire:click="openUserModal" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add New User
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-user text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Clients</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['clients'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-store text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Vendors</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['vendors'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-user-shield text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Admins</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['admins'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Verified</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['verified'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-exclamation-circle text-orange-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Unverified</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['unverified'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-calendar text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">This Month</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['this_month'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search by name or email..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Role Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select wire:model.live="roleFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Roles</option>
                        <option value="client">Clients</option>
                        <option value="vendor">Vendors</option>
                        <option value="admin">Administrators</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Status</label>
                    <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="verified">Email Verified</option>
                        <option value="unverified">Email Unverified</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Show</label>
                    <select wire:model.live="perPage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <button wire:click="clearFilters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </button>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">
                        Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                    </span>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" 
                                       wire:model.live="selectAll"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('name')">
                                    <span>User</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('role')">
                                    <span>Role</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('created_at')">
                                    <span>Joined</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Activity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 {{ $user->id === auth()->id() ? 'bg-blue-50' : '' }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" 
                                           wire:model.live="selectedUsers" 
                                           value="{{ $user->id }}"
                                           {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 disabled:opacity-50">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 {{ $this->getUserAvatarColor($user->id) }} rounded-full flex items-center justify-center mr-4">
                                            <span class="font-medium text-lg">{{ $this->getUserInitials($user->name) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                {{ $user->name }}
                                                @if($user->id === auth()->id())
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        You
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getRoleColor($user->role) }}">
                                        <i class="{{ $this->getRoleIcon($user->role) }} mr-1"></i>
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Unverified
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="openUserModal({{ $user->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button wire:click="toggleEmailVerification({{ $user->id }})" 
                                                class="text-blue-600 hover:text-blue-900" 
                                                title="{{ $user->email_verified_at ? 'Remove verification' : 'Verify email' }}">
                                            <i class="fas fa-{{ $user->email_verified_at ? 'times' : 'check' }}-circle"></i>
                                        </button>

                                        @if($user->id !== auth()->id())
                                            <button wire:click="impersonateUser({{ $user->id }})" 
                                                    class="text-purple-600 hover:text-purple-900" title="Impersonate">
                                                <i class="fas fa-user-secret"></i>
                                            </button>

                                            <button wire:click="deleteUser({{ $user->id }})" 
                                                    wire:confirm="Are you sure you want to delete '{{ $user->name }}'? This action cannot be undone."
                                                    class="text-red-600 hover:text-red-900" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif

                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" 
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                                <a href="{{ route('view.user.details',$user->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-eye mr-2"></i>View Profile
                                                </a>
                                                <a href="mailto:{{ $user->email }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-envelope mr-2"></i>Send Email
                                                </a>
                                                @if($user->role === 'vendor')
                                                    <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        <i class="fas fa-concierge-bell mr-2"></i>View Services
                                                    </a>
                                                @endif
                                                @if($user->role === 'client')
                                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        <i class="fas fa-calendar mr-2"></i>View Bookings
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                                        <p class="text-gray-500 mb-4">No users match your current filters.</p>
                                        @if($search || $roleFilter || $statusFilter)
                                            <button wire:click="clearFilters" class="text-blue-600 hover:text-blue-800">
                                                Clear all filters
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="bg-white px-6 py-3 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- User Modal -->
    @if($showUserModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeUserModal">
            <div class="relative top-20 mx-auto p-5 border max-w-md shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $isEditMode ? 'Edit User' : 'Create New User' }}
                    </h3>
                    <form wire:submit.prevent="saveUser">
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" 
                                       wire:model="name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                                       placeholder="Enter full name">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                <input type="email" 
                                       wire:model="email" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror"
                                       placeholder="Enter email address">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                <select wire:model="role" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-300 @enderror">
                                    <option value="client">Client</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="admin">Administrator</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Password {{ $isEditMode ? '(leave blank to keep current)' : '*' }}
                                </label>
                                <input type="password" 
                                       wire:model="password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 @enderror"
                                       placeholder="Enter password">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            @if(!$isEditMode || !empty($password))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                                    <input type="password" 
                                           wire:model="passwordConfirmation" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('passwordConfirmation') border-red-300 @enderror"
                                           placeholder="Confirm password">
                                    @error('passwordConfirmation')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Email Verified -->
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       wire:model="emailVerified" 
                                       id="emailVerified"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="emailVerified" class="ml-2 text-sm text-gray-700">
                                    Email is verified
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" 
                                    wire:click="closeUserModal"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                                <span wire:loading.remove wire:target="saveUser">
                                    {{ $isEditMode ? 'Update User' : 'Create User' }}
                                </span>
                                <span wire:loading wire:target="saveUser" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Actions Modal -->
    @if($showBulkModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="toggleBulkModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions</h3>
                    <p class="text-sm text-gray-500 mb-6">{{ count($selectedUsers) }} user(s) selected</p>
                    <div class="space-y-3">
                        <button wire:click="bulkVerifyEmail" 
                                wire:confirm="Are you sure you want to verify email for {{ count($selectedUsers) }} selected users?"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Verify Email
                        </button>
                        
                        <!-- Role Change Buttons -->
                        <div class="grid grid-cols-3 gap-2">
                            <button wire:click="bulkChangeRole('client')"
                                    wire:confirm="Are you sure you want to change {{ count($selectedUsers) }} selected users to Client role?"
                                    class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm transition-colors">
                                <i class="fas fa-user mr-1"></i>Client
                            </button>
                            <button wire:click="bulkChangeRole('vendor')"
                                    wire:confirm="Are you sure you want to change {{ count($selectedUsers) }} selected users to Vendor role?"
                                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm transition-colors">
                                <i class="fas fa-store mr-1"></i>Vendor
                            </button>
                            <button wire:click="bulkChangeRole('admin')"
                                    wire:confirm="Are you sure you want to change {{ count($selectedUsers) }} selected users to Admin role?"
                                    class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm transition-colors">
                                <i class="fas fa-user-shield mr-1"></i>Admin
                            </button>
                        </div>

                        <button wire:click="bulkDelete"
                                wire:confirm="Are you sure you want to delete {{ count($selectedUsers) }} selected users? This action cannot be undone."
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Selected
                        </button>
                        
                        <button wire:click="toggleBulkModal" 
                                class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

  
</div>


</div>
