<div>
<div>
    @section('title', 'Reviews & Reports Management')


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
                <h1 class="text-2xl font-bold text-gray-900">Reviews & Reports</h1>
                <p class="mt-1 text-sm text-gray-500">Monitor and moderate user reviews and feedback</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                @if(count($selectedReviews) > 0)
                    <button wire:click="toggleBulkModal" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-tasks mr-2"></i>
                        Bulk Actions ({{ count($selectedReviews) }})
                    </button>
                @endif
                <a href="{{ route('admin.reviews.export') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Export Reviews
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-star text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total Reviews</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
          

          



            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i class="fas fa-chart-line text-emerald-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Avg Rating</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['average_rating'], 1) }}</p>
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

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-flag text-orange-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Flagged</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['flagged'] }}</p>
                    </div>
                </div>
            </div>


        </div>

        <!-- Rating Distribution Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Rating Distribution</h3>
            <div class="space-y-3">
                @foreach($ratingDistribution as $rating => $count)
                    @php
                        $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                    @endphp
                    <div class="flex items-center">
                        <div class="flex items-center w-16">
                            <span class="text-sm font-medium w-2">{{ $rating }}</span>
                            <i class="fas fa-star text-yellow-400 text-sm ml-1"></i>
                        </div>
                        <div class="flex-1 mx-4">
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-3 rounded-full transition-all duration-300" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 w-16 text-right">
                            <span class="font-medium">{{ $count }}</span>
                            <span class="text-gray-400 ml-1">({{ number_format($percentage, 1) }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Reviews</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search reviews, users..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Rating Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                    <select wire:model.live="ratingFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Review Type</label>
                    <select wire:model.live="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="service">Services</option>
                        <option value="venue">Venues</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="hidden">Hidden</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Show</label>
                    <select wire:model.live="perPage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="15">15 per page</option>
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
                        Showing {{ $reviews->firstItem() ?? 0 }}-{{ $reviews->lastItem() ?? 0 }} of {{ $reviews->total() }} reviews
                    </span>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
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
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('user_id')">
                                    <span>Reviewer</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Review Content
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('rating')">
                                    <span>Rating</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type & Item
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('status')">
                                    <span>Status</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('created_at')">
                                    <span>Date</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reviews as $review)
                            <tr class="hover:bg-gray-50 {{ $review->isFlagged() ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" 
                                           wire:model.live="selectedReviews" 
                                           value="{{ $review->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 {{ $this->getUserAvatarColor($review->user->id) }} rounded-full flex items-center justify-center mr-3">
                                            <span class="font-medium text-sm">{{ $this->getUserInitials($review->user->name) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $review->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $review->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        @if($review->comment)
                                            <p class="text-sm text-gray-900 line-clamp-3">{{ $review->comment }}</p>
                                        @else
                                            <span class="text-sm italic text-gray-500">No comment provided</span>
                                        @endif
                                        @if($review->isFlagged())
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-flag mr-1"></i>Flagged
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex items-center">
                                            {!! $this->getRatingStars($review->rating) !!}
                                        </div>
                                        <span class="ml-2 text-sm font-medium {{ $this->getRatingColor($review->rating) }}">
                                            {{ $review->rating }}/5
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                               {{ $review->reviewable_type === 'App\Models\Service' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            <i class="fas fa-{{ $review->reviewable_type === 'App\Models\Service' ? 'concierge-bell' : 'building' }} mr-1"></i>
                                            {{ $review->reviewable_type_name }}
                                        </span>
                                        <div class="text-sm text-gray-900 mt-1">{{ $review->reviewable?->name ?? 'Deleted Item' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($review->status) }}">
                                        <div class="w-1.5 h-1.5 bg-current rounded-full mr-1"></div>
                                        {{ ucfirst($review->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $review->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $review->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if($review->status === 'pending')
                                            <button wire:click="quickApprove({{ $review->id }})" 
                                                    class="text-green-600 hover:text-green-900" title="Quick Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button wire:click="quickReject({{ $review->id }})" 
                                                    class="text-red-600 hover:text-red-900" title="Quick Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        <button wire:click="quickHide({{ $review->id }})" 
                                                class="text-gray-600 hover:text-gray-900" title="Hide">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>

                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" 
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                                <button wire:click="openModerationModal({{ $review->id }}, 'approve')" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-check mr-2 text-green-500"></i>Approve with Reason
                                                </button>
                                                <button wire:click="openModerationModal({{ $review->id }}, 'reject')" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-times mr-2 text-red-500"></i>Reject with Reason
                                                </button>
                                                <button wire:click="openModerationModal({{ $review->id }}, 'hide')" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-eye-slash mr-2 text-gray-500"></i>Hide with Reason
                                                </button>
                                                <hr class="my-1">
                                                <a href="mailto:{{ $review->user->email }}" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-envelope mr-2"></i>Contact Reviewer
                                                </a>
                                                <button wire:click="openModerationModal({{ $review->id }}, 'delete')" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                                    <i class="fas fa-trash mr-2"></i>Delete Review
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-star text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews found</h3>
                                        <p class="text-gray-500 mb-4">No reviews match your current filters.</p>
                                        @if($search || $ratingFilter || $typeFilter || $statusFilter)
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
            @if($reviews->hasPages())
                <div class="bg-white px-6 py-3 border-t border-gray-200">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Moderation Modal -->
    @if($showModerationModal && $moderatingReview)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModerationModal">
            <div class="relative top-20 mx-auto p-5 border max-w-2xl shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ ucfirst($moderationAction) }} Review
                    </h3>
                    
                    <!-- Review Preview -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex items-start space-x-3">
                            <div class="h-10 w-10 {{ $this->getUserAvatarColor($moderatingReview->user->id) }} rounded-full flex items-center justify-center">
                                <span class="font-medium text-sm">{{ $this->getUserInitials($moderatingReview->user->name) }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-medium text-gray-900">{{ $moderatingReview->user->name }}</h4>
                                    <div class="flex items-center">
                                        {!! $this->getRatingStars($moderatingReview->rating) !!}
                                        <span class="ml-1 text-sm text-gray-600">{{ $moderatingReview->rating }}/5</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">
                                    Review for {{ $moderatingReview->reviewable?->name }} 
                                    ({{ $moderatingReview->reviewable_type_name }})
                                </p>
                                @if($moderatingReview->comment)
                                    <p class="text-sm text-gray-900 bg-white rounded p-2 border">
                                        "{{ $moderatingReview->comment }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="moderateReview">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for {{ $moderationAction }} *
                            </label>
                            <textarea wire:model="moderationReason" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('moderationReason') border-red-300 @enderror"
                                      placeholder="Please provide a detailed reason for this action..."
                                      required></textarea>
                            @error('moderationReason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" 
                                    wire:click="closeModerationModal"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 rounded-lg text-white font-medium
                                           {{ $moderationAction === 'approve' ? 'bg-green-600 hover:bg-green-700' : 
                                              ($moderationAction === 'reject' ? 'bg-red-600 hover:bg-red-700' : 
                                               ($moderationAction === 'delete' ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-600 hover:bg-gray-700')) }}">
                                <i class="fas fa-{{ $moderationAction === 'approve' ? 'check' : ($moderationAction === 'reject' ? 'times' : ($moderationAction === 'delete' ? 'trash' : 'eye-slash')) }} mr-2"></i>
                                {{ ucfirst($moderationAction) }} Review
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
                    <p class="text-sm text-gray-500 mb-6">{{ count($selectedReviews) }} review(s) selected</p>
                    <div class="space-y-3">
                        <button wire:click="bulkApprove" 
                                wire:confirm="Are you sure you want to approve {{ count($selectedReviews) }} selected reviews?"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Approve Selected
                        </button>
                        <button wire:click="bulkReject"
                                wire:confirm="Are you sure you want to reject {{ count($selectedReviews) }} selected reviews?"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>Reject Selected
                        </button>
                        <button wire:click="bulkHide"
                                wire:confirm="Are you sure you want to hide {{ count($selectedReviews) }} selected reviews?"
                                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-eye-slash mr-2"></i>Hide Selected
                        </button>
                        <button wire:click="bulkDelete"
                                wire:confirm="Are you sure you want to delete {{ count($selectedReviews) }} selected reviews? This action cannot be undone."
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
