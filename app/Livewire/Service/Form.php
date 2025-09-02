<?php

namespace App\Livewire\Service;

use App\Models\Service;
use App\Models\User;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Form extends Component
{
    use WithFileUploads;

    protected $listeners = [
        'setLocation' => 'setLocation',
        'getCurrentLocation' => 'getCurrentLocation', 
        'locationError' => 'handleLocationError'
    ];

    public ?Service $service = null;
    public bool $isEdit = false;

    // Form fields
    public $user_id;
    public $category_id;
    public $name;
    public $description;
    public $pricing_model = 'flat';
    public $price;
    public $price_min;
    public $price_max;
    public $location_name;
    public $latitude;
    public $longitude;
    public $status = 'pending';
    public $gallery = [];
    public $existingImages = [];
    
    // File uploads
    public $newImages = [];
    public $imagesToDelete = [];

    // Location capture state
    public $locationCaptured = false;
    public $gettingLocation = false;

    // Search functionality
    public $searchProvider = '';
    public $showProviderDropdown = false;
    public $providers = [];

    protected $validationAttributes = [
        'user_id' => 'service provider',
        'category_id' => 'category',
        'pricing_model' => 'pricing model',
        'location_name' => 'location',
        'newImages.*' => 'image',
    ];

    

    public function mount(?Service $service = null)
    {
        if ($service && $service->exists) {
            $this->service = $service;
            $this->isEdit = true;
            $this->user_id = $service->user_id;
            $this->category_id = $service->category_id;
            $this->name = $service->name;
            $this->description = $service->description;
            $this->pricing_model = $service->pricing_model;
            $this->price = $service->price;
            $this->price_min = $service->price_min ?? $service->price;
            $this->price_max = $service->price_max ?? $service->price;
            $this->location_name = $service->location_name;
            $this->latitude = $service->latitude;
            $this->longitude = $service->longitude;
            $this->status = $service->status;
            $this->existingImages = $service->gallery ?? [];
            
            // Set location as captured if coordinates exist
            $this->locationCaptured = !empty($service->latitude) && !empty($service->longitude);
            $this->gettingLocation = false;
        } else {
            $this->user_id = auth()->id(); // Set current user as vendor by default
        }
        
        // Load initial provider list
        $this->providers = User::where('role', 'vendor')
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    protected function rules()
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'pricing_model' => ['required', 'in:flat,hourly,per_guest'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'price_min' => ['required', 'numeric', 'min:0'],
            'price_max' => ['required', 'numeric', 'min:0', 'gte:price_min'],
            'location_name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'status' => ['required', 'in:pending,active,suspended,rejected'],
            'newImages.*' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function updatedSearchProvider()
    {
        if (strlen($this->searchProvider) >= 2) {
            $this->providers = User::where('role', 'vendor')
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->searchProvider . '%')
                          ->orWhere('email', 'like', '%' . $this->searchProvider . '%');
                })
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->showProviderDropdown = true;
        } else {
            $this->showProviderDropdown = false;
        }
    }

    public function selectProvider($userId, $userName)
    {
        $this->user_id = $userId;
        $this->searchProvider = $userName;
        $this->showProviderDropdown = false;
    }

    public function updatedNewImages()
    {
        $this->validate([
            'newImages.*' => 'image|max:2048',
        ]);
    }

    public function removeExistingImage($index)
    {
        if (isset($this->existingImages[$index])) {
            $this->imagesToDelete[] = $this->existingImages[$index];
            unset($this->existingImages[$index]);
            $this->existingImages = array_values($this->existingImages);
        }
    }

    public function removeNewImage($index)
    {
        if (isset($this->newImages[$index])) {
            unset($this->newImages[$index]);
            $this->newImages = array_values($this->newImages);
        }
    }

    public function getCurrentLocation()
    {
        if (!$this->gettingLocation && !$this->locationCaptured) {
            $this->gettingLocation = true;
            $this->dispatch('getCurrentLocation');
        }
    }

    public function setLocation($data)
    {
        $this->latitude = $data['latitude'] ?? null;
        $this->longitude = $data['longitude'] ?? null;
        
        if (!empty($data['locationName'])) {
            $this->location_name = $data['locationName'];
        }
        
        $this->locationCaptured = true;
        $this->gettingLocation = false;
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

    public function save()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for debugging
            \Log::error('Service validation failed', [
                'errors' => $e->errors(),
                'data' => $this->only([
                    'user_id', 'category_id', 'name', 'description', 'pricing_model',
                    'price', 'price_min', 'price_max', 'location_name', 'latitude', 
                    'longitude', 'status'
                ])
            ]);
            
            // Set session flash with validation errors for user feedback
            session()->flash('error', 'Please fix the validation errors below.');
            throw $e;
        }

        try {
            // Handle image uploads
            $galleryImages = $this->existingImages;

            foreach ($this->newImages as $image) {
                $path = $image->store('services', 'public');
                $galleryImages[] = $path;
            }

            // Delete removed images from storage
            foreach ($this->imagesToDelete as $imageUrl) {
                $path = str_replace('/storage/', '', $imageUrl);
                Storage::disk('public')->delete($path);
            }

            $serviceData = [
                'user_id' => $this->user_id,
                'category_id' => $this->category_id ?: null,
                'name' => $this->name,
                'description' => $this->description,
                'pricing_model' => $this->pricing_model,
                'price' => $this->price ?: null, // Convert empty string to null
                'price_min' => $this->price_min,
                'price_max' => $this->price_max,
                'location_name' => $this->location_name,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'status' => $this->status,
                'gallery' => $galleryImages,
            ];

            if ($this->isEdit) {
                $this->service->update($serviceData);
                session()->flash('success', 'Service updated successfully.');
            } else {
                Service::create($serviceData);
                session()->flash('success', 'Service created successfully.');
            }

            return redirect()->route('services.management');

        } catch (\Exception $e) {
            \Log::error('Error saving service', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'serviceData' => $serviceData ?? null
            ]);
            session()->flash('error', 'An error occurred while saving the service: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('services.management');
    }

    public function render()
    {
        $categories = Category::where('type', 'service')->orderBy('name')->get();
        $selectedProvider = null;
        
        if ($this->user_id) {
            $selectedProvider = User::find($this->user_id);
        }

        return view('livewire.service.form', [
            'categories' => $categories,
            'selectedProvider' => $selectedProvider,
        ]);
    }



}