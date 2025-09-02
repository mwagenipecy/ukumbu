<div class="space-y-4">
    <!-- Sidebar Card: Create Event + My Events -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-900">Events</h3>
            <button wire:click="openCreateModal" class="inline-flex items-center text-sm px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-plus mr-2"></i>
                Create Event
            </button>
        </div>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200">
                <span class="text-sm text-gray-700">My Events ({{ $events->total() }})</span>
                <i class="fas fa-chevron-down text-gray-500 text-xs" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="mt-2 max-h-64 overflow-y-auto space-y-1">
                @forelse($events as $event)
                    <a href="#" class="block px-3 py-2 text-sm text-gray-700 rounded-md hover:bg-gray-50 border border-transparent hover:border-gray-200">
                        <div class="font-medium text-gray-900 truncate">{{ $event->title }}</div>
                        <div class="text-xs text-gray-500">{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M j, Y') : 'No date' }}</div>
                    </a>
                @empty
                    <div class="text-sm text-gray-500 px-3 py-2">No events yet</div>
                @endforelse
                <div class="px-3 py-2">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-center justify-center" wire:click="showCreateModal=false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6" wire:click.stop>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Create Event</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Title *</label>
                        <input type="text" wire:model.defer="title" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Wedding Reception" />
                        @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Description</label>
                        <textarea wire:model.defer="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tell guests about your event..."></textarea>
                        @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Start Date</label>
                            <input type="date" wire:model.defer="start_date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            @error('start_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">End Date</label>
                            <input type="date" wire:model.defer="end_date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            @error('end_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Location</label>
                        <input type="text" wire:model.defer="location_name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Dar es Salaam" />
                        @error('location_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Status</label>
                        <select wire:model.defer="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-6">
                    <button wire:click="showCreateModal=false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Cancel</button>
                    <button wire:click="createEvent" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Continue</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Bank Account Modal -->
    @if($showBankModal)
        <div class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-center justify-center" wire:click="showBankModal=false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6" wire:click.stop>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Bank Account</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Bank Name *</label>
                        <input type="text" wire:model.defer="bank_name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., CRDB, NMB" />
                        @error('bank_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Account Name *</label>
                        <input type="text" wire:model.defer="account_name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., John Doe" />
                        @error('account_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Account Number *</label>
                        <input type="text" wire:model.defer="account_number" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 0123456789" />
                        @error('account_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Branch</label>
                            <input type="text" wire:model.defer="branch_name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">SWIFT Code</label>
                            <input type="text" wire:model.defer="swift_code" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus;border-blue-500" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-6">
                    <button wire:click="showBankModal=false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Skip</button>
                    <button wire:click="saveBankAccount" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Bank Account</button>
                </div>
            </div>
        </div>
    @endif
</div>
