<?php

namespace App\Livewire\Client;

use App\Models\Service;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceList extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $priceRange = '';
    public $pricingModelFilter = '';
    public $sortBy = 'latest';
    public $userLatitude = null;
    public $userLongitude = null;

    protected $listeners = [
        'locationDetected' => 'setUserLocation',
        'likeToggled' => '$refresh',
        'bookmarkToggled' => '$refresh'
    ];

    public function mount()
    {
        // Initialize any default values
    }

    public function setUserLocation($latitude, $longitude)
    {
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedPriceRange()
    {
        $this->resetPage();
    }

    public function updatedPricingModelFilter()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function getServicesProperty()
    {
        $query = Service::with(['user', 'category', 'likes', 'bookmarks', 'reviews'])
                        ->where('status', 'active');

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location_name', 'like', '%' . $this->search . '%');
            });
        }

        // Category filter
        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        // Pricing model filter
        if ($this->pricingModelFilter) {
            $query->where('pricing_model', $this->pricingModelFilter);
        }

        // Price range filter
        if ($this->priceRange) {
            switch ($this->priceRange) {
                case 'under_50k':
                    $query->where('price', '<', 50000);
                    break;
                case '50k_200k':
                    $query->whereBetween('price', [50000, 200000]);
                    break;
                case 'over_200k':
                    $query->where('price', '>', 200000);
                    break;
            }
        }

        // Sorting
        if ($this->userLatitude && $this->userLongitude && $this->sortBy === 'proximity') {
            $query->selectRaw("*, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + 
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance", [$this->userLatitude, $this->userLongitude, $this->userLatitude])
            ->orderBy('distance');
        } else {
            switch ($this->sortBy) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular':
                    $query->withCount('likes')->orderBy('likes_count', 'desc');
                    break;
                case 'latest':
                default:
                    $query->latest();
            }
        }

        return $query->paginate(12);
    }

    public function getCategoriesProperty()
    {
        return Category::where('type', 'service')->orderBy('name')->get();
    }

    public function getServiceRating($service)
    {
        return $service->reviews()->avg('rating') ?? 0;
    }

    public function render()
    {
        return view('livewire.client.service-list', [
            'services' => $this->services,
            'categories' => $this->categories,
        ])->layout('layouts.app');
    }
}

