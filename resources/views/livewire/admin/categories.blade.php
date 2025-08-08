<div>

@section('title', 'Categories Management')

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Categories Management</h2>
            <p class="mt-1 text-sm text-gray-600">Manage venue and service categories</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button 
                wire:click="openCreateModal"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
            >
                <i class="fas fa-plus mr-2"></i>
                Add Category
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Categories</label>
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by name..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Type Filter -->
            <div>
                <label for="filterType" class="block text-sm font-medium text-gray-700 mb-1">Filter by Type</label>
                <select 
                    wire:model.live="filterType"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">All Types</option>
                    <option value="venue">Venues</option>
                    <option value="service">Services</option>
                </select>
            </div>

            <!-- Stats -->
            <div class="flex items-end">
                <div class="bg-gray-50 rounded-lg p-3 w-full">
                    <div class="text-sm text-gray-600">Total Categories</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $categories->total() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Categories List</h3>
        </div>

        @if($categories->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usage Count
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-{{ $category->type === 'venue' ? 'blue' : 'green' }}-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-{{ $category->type === 'venue' ? 'building' : 'concierge-bell' }} text-{{ $category->type === 'venue' ? 'blue' : 'green' }}-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->type === 'venue' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($category->type === 'venue')
                                        {{ $category->venues_count }} venues
                                    @else
                                        {{ $category->services_count }} services
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $category->created_at }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button 
                                            wire:click="openEditModal({{ $category->id }})"
                                            class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors duration-200"
                                            title="Edit Category"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button 
                                            onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')"
                                            class="text-red-600 hover:text-red-900 p-1 rounded transition-colors duration-200 {{ ($category->venues_count + $category->services_count) > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            title="{{ ($category->venues_count + $category->services_count) > 0 ? 'Cannot delete - category in use' : 'Delete Category' }}"
                                            {{ ($category->venues_count + $category->services_count) > 0 ? 'disabled' : '' }}
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $categories->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-tags text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
                <p class="text-gray-500 mb-6">Get started by creating your first category.</p>
                <button 
                    wire:click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Add Category
                </button>
            </div>
        @endif
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="resetModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingCategoryId ? 'Edit Category' : 'Add New Category' }}
                        </h3>
                        <button wire:click="resetModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Modal Form -->
                    <form wire:submit.prevent="save">
                        <!-- Category Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Category Name *
                            </label>
                            <input 
                                type="text" 
                                wire:model="name"
                                placeholder="Enter category name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category Type -->
                        <div class="mb-6">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Category Type *
                            </label>
                            <select 
                                wire:model="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror"
                            >
                                <option value="venue">Venue Category</option>
                                <option value="service">Service Category</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Modal Buttons -->
                        <div class="flex items-center justify-end space-x-3">
                            <button 
                                type="button"
                                wire:click="resetModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors duration-200"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="save">
                                    {{ $editingCategoryId ? 'Update Category' : 'Create Category' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    {{ $editingCategoryId ? 'Updating...' : 'Creating...' }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading Overlay -->
    <div wire:loading.flex class="fixed inset-0 bg-gray-600 bg-opacity-50 z-40 items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-lg">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-gray-700">Processing...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(categoryId, categoryName) {
    if (confirm(`Are you sure you want to delete the category "${categoryName}"? This action cannot be undone.`)) {
        @this.call('delete', categoryId);
    }
}
</script>
@endpush


</div>
