<div>
<div>
    @section('title', $isEdit ? 'Edit Service' : 'Create Service')

    @section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $isEdit ? 'Edit Service' : 'Create New Service' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $isEdit ? 'Update service information and settings' : 'Add a new service to the platform' }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="cancel" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="save" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEdit ? 'Update Service' : 'Create Service' }}
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>

        <!-- Form -->
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Service Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service Name *</label>
                                <input type="text" 
                                       wire:model="name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                                       placeholder="Enter service name">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select wire:model="category_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-300 @enderror">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status (Admin only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select wire:model="status" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror">
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea wire:model="description" 
                                          rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 @enderror"
                                          placeholder="Describe your service..."></textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Pricing Model -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pricing Model *</label>
                                <select wire:model="pricing_model" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pricing_model') border-red-300 @enderror">
                                    <option value="flat">Flat Rate</option>
                                    <option value="hourly">Per Hour</option>
                                    <option value="per_guest">Per Guest</option>
                                </select>
                                @error('pricing_model')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Price (TSh) *
                                    @if($pricing_model === 'hourly')
                                        <span class="text-gray-500">per hour</span>
                                    @elseif($pricing_model === 'per_guest')
                                        <span class="text-gray-500">per guest</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           wire:model="price" 
                                           step="0.01"
                                           min="0"
                                           class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-300 @enderror"
                                           placeholder="0.00">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                        <span class="text-gray-500">TSh</span>
                                    </div>
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Location Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                        <div class="space-y-4">
                            <!-- Location Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location Name *</label>
                                <input type="text" 
                                       wire:model="location_name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location_name') border-red-300 @enderror"
                                       placeholder="e.g., Dar es Salaam, Kinondoni">
                                @error('location_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Coordinates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitude *</label>
                                    <input type="number" 
                                           wire:model="latitude" 
                                           step="any"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('latitude') border-red-300 @enderror"
                                           placeholder="-6.7924">
                                    @error('latitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitude *</label>
                                    <input type="number" 
                                           wire:model="longitude" 
                                           step="any"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('longitude') border-red-300 @enderror"
                                           placeholder="39.2083">
                                    @error('longitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Get Current Location Button -->
                            <div class="flex justify-start">
                                <button type="button" 
                                        wire:click="getCurrentLocation"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                    Use Current Location
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Provider Selection -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Service Provider</h3>
                        <div class="space-y-4">
                            <!-- Provider Search -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search Provider *</label>
                                <input type="text" 
                                       wire:model.live.debounce.300ms="searchProvider"
                                       wire:focus="$set('showProviderDropdown', true)"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('user_id') border-red-300 @enderror"
                                       placeholder="Type provider name or email...">
                                
                                <!-- Provider Dropdown -->
                                @if($showProviderDropdown && count($providers) > 0)
                                    <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                        @foreach($providers as $provider)
                                            <div wire:click="selectProvider({{ $provider->id }}, '{{ $provider->name }}')"
                                                 class="px-4 py-2 hover:bg-gray-50 cursor-pointer flex items-center">
                                                <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-blue-600 font-medium text-sm">
                                                        {{ strtoupper(substr($provider->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $provider->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $provider->email }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            @error('user_id')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Selected Provider -->
                            @if($selectedProvider)
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-blue-600 font-medium">
                                                {{ strtoupper(substr($selectedProvider->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ $selectedProvider->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $selectedProvider->email }}</div>
                                        </div>
                                        <button type="button" 
                                                wire:click="$set('user_id', null)"
                                                class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Image Gallery -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Service Gallery</h3>
                        
                        <!-- Existing Images -->
                        @if(count($existingImages) > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Current Images</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($existingImages as $index => $image)
                                        <div class="relative group">
                                            <img src="{{ $image }}" 
                                                 alt="Service image" 
                                                 class="w-full h-24 object-cover rounded-lg">
                                            <button type="button" 
                                                    wire:click="removeExistingImage({{ $index }})"
                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- New Image Upload -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Add New Images</label>
                                <input type="file" 
                                       wire:model="newImages" 
                                       multiple 
                                       accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Max 2MB per image. JPG, PNG, GIF supported.</p>
                                @error('newImages.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Images Preview -->
                            @if(count($newImages) > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">New Images Preview</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($newImages as $index => $image)
                                            <div class="relative group">
                                                @if($image->isPreviewable())
                                                    <img src="{{ $image->temporaryUrl() }}" 
                                                         alt="Preview" 
                                                         class="w-full h-24 object-cover rounded-lg">
                                                @endif
                                                <button type="button" 
                                                        wire:click="removeNewImage({{ $index }})"
                                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Upload Progress -->
                            <div wire:loading wire:target="newImages" class="text-center">
                                <div class="inline-flex items-center text-sm text-blue-600">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Uploading images...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Geolocation functionality
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('getCurrentLocation', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            // Reverse geocoding to get location name (optional)
                            fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`)
                                .then(response => response.json())
                                .then(data => {
                                    const locationName = data.city || data.locality || data.countryName || 'Unknown Location';
                                    Livewire.dispatch('setLocation', {
                                        latitude: lat,
                                        longitude: lng,
                                        locationName: locationName
                                    });
                                })
                                .catch(() => {
                                    Livewire.dispatch('setLocation', {
                                        latitude: lat,
                                        longitude: lng
                                    });
                                });
                        },
                        function(error) {
                            alert('Error getting location: ' + error.message);
                        }
                    );
                } else {
                    alert('Geolocation is not supported by this browser.');
                }
            });
        });
    </script>
    @endpush
    @endsection
</div>

</div>
