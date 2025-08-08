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
    public $location_name = '';
    public $latitude = '';
    public $longitude = '';
    public $category_id = '';
    public $user_id = '';
    public $status = 'active';
    public $gallery = [];
    public $newImages = [];

    protected $listeners = [
        'venueDeleted' => '$refresh',
        'closeModal' => 'resetModal',
        'locationSelected' => 'updateLocation'
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'base_price' => 'required|numeric|min:0',
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
        'base_price.required' => 'Base price is required.',
        'base_price.numeric' => 'Base price must be a valid number.',
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
        $this->location_name = $venue->location_name;
        $this->latitude = $venue->latitude;
        $this->longitude = $venue->longitude;
        $this->category_id = $venue->category_id;
        $this->user_id = $venue->user_id;
        $this->status = $venue->status;
        $this->gallery = $venue->gallery ?? [];
        
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
        $this->location_name = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->category_id = '';
        $this->user_id = '';
        $this->status = 'active';
        $this->gallery = [];
        $this->newImages = [];
        $this->resetValidation();
    }

    public function updateLocation($latitude, $longitude, $locationName)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->location_name = $locationName;
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
        $this->validate();

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
                'base_price' => $this->base_price,
                'location_name' => $this->location_name,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'category_id' => $this->category_id,
                'user_id' => $this->user_id,
                'status' => $this->status,
                'gallery' => $imagesPaths,
            ];

            if ($this->editingVenueId) {
                // Update existing venue
                $venue = Venue::findOrFail($this->editingVenueId);
                $venue->update($data);
                
                session()->flash('success', 'Venue updated successfully!');
            } else {
                // Create new venue
                Venue::create($data);
                
                session()->flash('success', 'Venue created successfully!');
            }

            $this->resetModal();
            $this->dispatch('venueUpdated');
        } catch (\Exception $e) {
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

    public function render()
    {
        return view('livewire.admin.venues', [
            'venues' => $this->venues,
            'categories' => $this->categories,
            'vendors' => $this->vendors,
        ]);
    }
}