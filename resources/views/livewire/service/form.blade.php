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

        <!-- Validation Errors Summary -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Please fix the following errors:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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

                            <!-- Price Range -->
                            <div class="space-y-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Price Range (TSh) *
                                    @if($pricing_model === 'hourly')
                                        <span class="text-gray-500">per hour</span>
                                    @elseif($pricing_model === 'per_guest')
                                        <span class="text-gray-500">per guest</span>
                                    @endif
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <div class="relative">
                                            <input type="number" 
                                                   wire:model="price_min" 
                                                   step="0.01"
                                                   min="0"
                                                   class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price_min') border-red-300 @enderror"
                                                   placeholder="Min price">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                                <span class="text-gray-500 text-sm">TSh</span>
                                            </div>
                                        </div>
                                        @error('price_min')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <div class="relative">
                                            <input type="number" 
                                                   wire:model="price_max" 
                                                   step="0.01"
                                                   min="0"
                                                   class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price_max') border-red-300 @enderror"
                                                   placeholder="Max price">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                                <span class="text-gray-500 text-sm">TSh</span>
                                            </div>
                                        </div>
                                        @error('price_max')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
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
                            <div class="flex justify-start space-x-3">
                                @if(!$locationCaptured)
                                    <button type="button" 
                                            wire:click="getCurrentLocation"
                                            @disabled($gettingLocation)
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        @if($gettingLocation)
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Getting Location...
                                        @else
                                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                            Use Current Location
                                        @endif
                                    </button>
                                @else
                                    <div class="flex items-center space-x-3">
                                        <div class="inline-flex items-center px-4 py-2 border border-green-300 bg-green-50 rounded-lg text-sm text-green-700">
                                            <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                            Location Captured Successfully
                                        </div>
                                        <button type="button" 
                                                wire:click="resetLocationCapture"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-redo mr-2"></i>
                                            Re-capture
                                        </button>
                                    </div>
                                @endif
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
        // Geolocation functionality for service creation
        document.addEventListener('livewire:initialized', function() {
            console.log('Services form: Livewire initialized');
            let isGettingLocation = false;

            Livewire.on('getCurrentLocation', function() {
                console.log('Services form: getCurrentLocation event received');
                
                // Prevent multiple simultaneous location requests
                if (isGettingLocation) {
                    console.log('Services form: Already getting location, returning');
                    return;
                }

                if (navigator.geolocation) {
                    console.log('Services form: Geolocation available, requesting position');
                    isGettingLocation = true;
                    
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            console.log('Services form: Position received:', position.coords);
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            // Reverse geocoding to get location name (optional)
                            console.log('Services form: Starting reverse geocoding');
                            fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`)
                                .then(response => response.json())
                                .then(data => {
                                    console.log('Services form: Reverse geocoding successful:', data);
                                    const locationName = data.city || data.locality || data.countryName || 'Unknown Location';
                                    const locationData = {
                                        latitude: lat,
                                        longitude: lng,
                                        locationName: locationName
                                    };
                                    console.log('Services form: Dispatching setLocation with:', locationData);
                                    Livewire.dispatch('setLocation', locationData);
                                })
                                .catch((error) => {
                                    console.log('Services form: Reverse geocoding failed, using coordinates only:', error);
                                    const locationData = {
                                        latitude: lat,
                                        longitude: lng
                                    };
                                    console.log('Services form: Dispatching setLocation with:', locationData);
                                    Livewire.dispatch('setLocation', locationData);
                                })
                                .finally(() => {
                                    console.log('Services form: Setting isGettingLocation to false');
                                    isGettingLocation = false;
                                });
                        },
                        function(error) {
                            console.error('Services form: Geolocation error:', error);
                            isGettingLocation = false;
                            // Reset the UI state on error
                            Livewire.dispatch('locationError');
                            alert('Error getting location: ' + error.message + '. Please enter coordinates manually or try again.');
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 60000
                        }
                    );
                } else {
                    console.error('Services form: Geolocation not supported');
                    alert('Geolocation is not supported by this browser. Please enter coordinates manually.');
                    Livewire.dispatch('locationError');
                }
            });
            
            console.log('Services form: Event listeners registered');
        });
    </script>
    @endpush   
</div>
