<?php

namespace App\Livewire\Admin;

use App\Models\Venue;
use App\Models\Category;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Venues extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $ownerFilter = '';
    public $showModal = false;
    public $showViewModal = false;
    public $editingVenueId = null;
    public $viewingVenue = null;
    
    // Form fields
    public $name = '';
    public $description = '';
    public $base_price = '';
    public $price_min = '';
    public $price_max = '';
    public $price_type = 'per_event';
    public $location_name = '';
    public $latitude = '';
    public $longitude = '';
    public $category_id = '';
    public $user_id = '';
    public $status = 'active';
    public $gallery = [];
    public $newImages = [];
    public $locationCaptured = false;
    public $gettingLocation = false;

    protected $listeners = [
        'venueDeleted' => '$refresh',
        'closeModal' => 'resetModal',
        'locationSelected' => 'updateLocation',
        'getCurrentLocation' => 'getCurrentLocation',
        'locationError' => 'handleLocationError'
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'base_price' => 'nullable|numeric|min:0',
        'price_min' => 'required|numeric|min:0',
        'price_max' => 'required|numeric|min:0|gte:price_min',
        'price_type' => 'required|in:per_event,per_hour,per_day',
        'location_name' => 'required|string|max:255',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'category_id' => 'required|exists:categories,id',
        'user_id' => 'required|exists:users,id',
        'status' => 'required|in:active,inactive,pending',
        'newImages.*' => 'nullable|image|max:2048', // 2MB max per image
    ];

    protected $messages = [
        'name.required' => 'Venue name is required.',
        'price_min.required' => 'Minimum price is required.',
        'price_min.numeric' => 'Minimum price must be a valid number.',
        'price_min.min' => 'Minimum price must be at least 0.',
        'price_max.required' => 'Maximum price is required.',
        'price_max.numeric' => 'Maximum price must be a valid number.',
        'price_max.min' => 'Maximum price must be at least 0.',
        'price_max.gte' => 'Maximum price must be greater than or equal to minimum price.',
        'price_type.required' => 'Price type is required.',
        'latitude.required' => 'Latitude is required.',
        'latitude.between' => 'Latitude must be between -90 and 90.',
        'longitude.required' => 'Longitude is required.',
        'longitude.between' => 'Longitude must be between -180 and 180.',
        'category_id.required' => 'Please select a category.',
        'user_id.required' => 'Please select a venue owner.',
        'newImages.*.image' => 'All uploaded files must be images.',
        'newImages.*.max' => 'Each image must not exceed 2MB.',
    ];

    public function mount()
    {
        $this->statusFilter = '';
        $this->categoryFilter = '';
        $this->ownerFilter = '';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedOwnerFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        if (!$this->checkDataAvailability()) {
            return;
        }
        
        $this->resetModal();
        $this->showModal = true;
    }

    public function openEditModal($venueId)
    {
        $venue = Venue::with(['category', 'user'])->findOrFail($venueId);
        
        $this->editingVenueId = $venueId;
        $this->name = $venue->name;
        $this->description = $venue->description;
        $this->base_price = $venue->base_price;
        $this->price_min = $venue->price_min ?? $venue->base_price;
        $this->price_max = $venue->price_max ?? $venue->base_price;
        $this->price_type = $venue->price_type ?? 'per_event';
        $this->location_name = $venue->location_name;
        $this->latitude = $venue->latitude;
        $this->longitude = $venue->longitude;
        $this->category_id = $venue->category_id;
        $this->user_id = $venue->user_id;
        $this->status = $venue->status;
        $this->gallery = $venue->gallery ?? [];
        
        // Set location as captured if coordinates exist
        $this->locationCaptured = !empty($venue->latitude) && !empty($venue->longitude);
        $this->gettingLocation = false;
        
        $this->showModal = true;
    }

    public function openViewModal($venueId)
    {
        $this->viewingVenue = Venue::with(['category', 'user', 'bookings', 'reviews.user', 'likes', 'bookmarks'])
                                  ->findOrFail($venueId);
        $this->showViewModal = true;
    }

    public function resetModal()
    {
        $this->showModal = false;
        $this->showViewModal = false;
        $this->editingVenueId = null;
        $this->viewingVenue = null;
        $this->name = '';
        $this->description = '';
        $this->base_price = '';
        $this->price_min = '';
        $this->price_max = '';
        $this->price_type = 'per_event';
        $this->location_name = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->category_id = '';
        $this->user_id = '';
        $this->status = 'active';
        $this->gallery = [];
        $this->newImages = [];
        $this->locationCaptured = false;
        $this->gettingLocation = false;
        $this->resetValidation();
    }

    public function updateLocation($latitude, $longitude, $locationName)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        if ($locationName) {
            $this->location_name = $locationName;
        }
        $this->locationCaptured = true;
        $this->gettingLocation = false;
    }

    public function getCurrentLocation()
    {
        if (!$this->gettingLocation && !$this->locationCaptured) {
            $this->gettingLocation = true;
            $this->dispatch('getCurrentLocation');
        }
    }

    public function resetLocationCapture()
    {
        $this->locationCaptured = false;
        $this->gettingLocation = false;
        $this->latitude = '';
        $this->longitude = '';
        $this->location_name = '';
    }

    public function handleLocationError()
    {
        $this->gettingLocation = false;
        $this->locationCaptured = false;
    }

    public function removeImage($index)
    {
        if (isset($this->gallery[$index])) {
            // Delete from storage
            Storage::disk('public')->delete($this->gallery[$index]);
            // Remove from array
            array_splice($this->gallery, $index, 1);
        }
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for debugging
            \Log::error('Venue validation failed', [
                'errors' => $e->errors(),
                'data' => $this->only([
                    'name', 'description', 'base_price', 'price_min', 'price_max', 
                    'price_type', 'location_name', 'latitude', 'longitude', 
                    'category_id', 'user_id', 'status'
                ])
            ]);
            
            // Set session flash with validation errors for user feedback
            session()->flash('error', 'Please fix the validation errors below.');
            throw $e;
        }

        try {
            // Handle image uploads
            $imagesPaths = $this->gallery;
            
            if (!empty($this->newImages)) {
                foreach ($this->newImages as $image) {
                    $path = $image->store('venues', 'public');
                    $imagesPaths[] = $path;
                }
            }

            $data = [
                'name' => $this->name,
                'description' => $this->description,
                'base_price' => $this->base_price ?: null, // Convert empty string to null
                'price_min' => $this->price_min,
                'price_max' => $this->price_max,
                'price_type' => $this->price_type,
                'location_name' => $this->location_name,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'category_id' => $this->category_id,
                'user_id' => $this->user_id,
                'status' => $this->status,
                'gallery' => $imagesPaths,
            ];

            // Log the data being saved for debugging
            \Log::info('Attempting to save venue', [
                'editing' => $this->editingVenueId,
                'data' => $data
            ]);

            if ($this->editingVenueId) {
                // Update existing venue
                $venue = Venue::findOrFail($this->editingVenueId);
                $venue->update($data);
                
                \Log::info('Venue updated', ['id' => $venue->id]);
                session()->flash('success', 'Venue updated successfully!');
            } else {
                // Create new venue
                $venue = Venue::create($data);
                
                \Log::info('Venue created', ['id' => $venue->id, 'name' => $venue->name]);
                session()->flash('success', 'Venue created successfully!');
            }

            $this->resetModal();
            $this->dispatch('venueUpdated');
        } catch (\Exception $e) {
            \Log::error('Error saving venue', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $data ?? null
            ]);
            session()->flash('error', 'An error occurred while saving the venue: ' . $e->getMessage());
        }
    }

    public function updateStatus($venueId, $newStatus)
    {
        try {
            $venue = Venue::findOrFail($venueId);
            $venue->update(['status' => $newStatus]);
            
            session()->flash('success', 'Venue status updated to ' . $newStatus . '!');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the venue status.');
        }
    }

    public function delete($venueId)
    {
        try {
            $venue = Venue::findOrFail($venueId);
            
            // Check if venue has active bookings
            $activeBookings = $venue->bookings()->whereIn('status', ['pending', 'confirmed'])->count();
            
            if ($activeBookings > 0) {
                session()->flash('error', 'Cannot delete venue with active bookings. Please complete or cancel all bookings first.');
                return;
            }

            // Delete gallery images
            if (!empty($venue->gallery)) {
                foreach ($venue->gallery as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $venue->delete();
            session()->flash('success', 'Venue deleted successfully!');
            $this->dispatch('venueDeleted');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while deleting the venue.');
        }
    }

    public function getVenuesProperty()
    {
        $query = Venue::with(['category', 'user'])
                     ->withCount(['bookings', 'reviews', 'likes']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('location_name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->ownerFilter) {
            $query->where('user_id', $this->ownerFilter);
        }

        return $query->latest()->paginate(10);
    }

    public function getCategoriesProperty()
    {
        return Category::where('type', 'venue')->orderBy('name')->get();
    }

    public function getVendorsProperty()
    {
        return User::where('role', 'vendor')->orderBy('name')->get();
    }

    public function checkDataAvailability()
    {
        $categories = $this->categories;
        $vendors = $this->vendors;
        
        if ($categories->isEmpty()) {
            session()->flash('warning', 'No venue categories found. Please create venue categories first.');
            return false;
        }
        
        if ($vendors->isEmpty()) {
            session()->flash('warning', 'No vendors found. Please create vendor users first.');
            return false;
        }
        
        return true;
    }

    public function render()
    {
        return view('livewire.admin.venues', [
            'venues' => $this->venues,
            'categories' => $this->categories,
            'vendors' => $this->vendors,
        ]);
    }
}