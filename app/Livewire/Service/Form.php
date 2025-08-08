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

    public ?Service $service = null;
    public bool $isEdit = false;

    // Form fields
    public $user_id;
    public $category_id;
    public $name;
    public $description;
    public $pricing_model = 'flat';
    public $price;
    public $location_name;
    public $latitude;
    public $longitude;
    public $status = 'pending';
    public $gallery = [];
    public $existingImages = [];
    
    // File uploads
    public $newImages = [];
    public $imagesToDelete = [];

    // Search functionality
    public $searchProvider = '';
    public $showProviderDropdown = false;
    public $providers = [];

    protected function rules()
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'pricing_model' => ['required', 'in:flat,hourly,per_guest'],
            'price' => ['required', 'numeric', 'min:0'],
            'location_name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'status' => ['required', 'in:pending,active,suspended,rejected'],
            'newImages.*' => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected $validationAttributes = [
        'user_id' => 'service provider',
        'category_id' => 'category',
        'pricing_model' => 'pricing model',
        'location_name' => 'location',
        'newImages.*' => 'image',
    ];

    public function mount(?Service $service = null)
    {
        if ($service->exists) {
            $this->service = $service;
            $this->isEdit = true;
            $this->fill($service->toArray());
            $this->existingImages = $service->gallery ?? [];
        }

        $this->providers = User::where('role', 'vendor')
            ->orderBy('name')
            ->limit(10)
            ->get();
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
        $this->dispatch('getCurrentLocation');
    }

    public function setLocation($latitude, $longitude, $locationName = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        
        if ($locationName) {
            $this->location_name = $locationName;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // Handle image uploads
            $galleryImages = $this->existingImages;

            foreach ($this->newImages as $image) {
                $path = $image->store('services', 'public');
                $galleryImages[] = Storage::url($path);
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
                'price' => $this->price,
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

            return redirect()->route('admin.services.index');

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the service. Please try again.');
        }
    }

    public function cancel()
    {
        return redirect()->route('admin.services.index');
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