<?php

namespace App\Livewire\Client;

use App\Models\Venue;
use App\Models\Service;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Homepage extends Component
{
    use WithPagination;

    public $searchQuery = '';
    public $selectedCategory = '';
    public $viewType = 'venues'; // 'venues' or 'services'
    public $userLatitude = null;
    public $userLongitude = null;
    public $priceRange = '';
    public $sortBy = 'proximity'; // 'proximity', 'price_low', 'price_high', 'popular'

    protected $listeners = [
        'locationDetected' => 'setUserLocation',
        'likeToggled' => '$refresh',
        'bookmarkToggled' => '$refresh'
    ];

    public function mount()
    {
        $this->viewType = 'venues';
    }

    public function setUserLocation($latitude, $longitude)
    {
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;
        $this->resetPage();
    }

    public function switchView($type)
    {
        $this->viewType = $type;
        $this->selectedCategory = '';
        $this->resetPage();
    }

    public function updatedSearchQuery()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatedPriceRange()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function getVenuesProperty()
    {
        $query = Venue::query()->with(['user', 'category', 'likes', 'bookmarks']);

        // Search filter
        if ($this->searchQuery) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('location_name', 'like', '%' . $this->searchQuery . '%');
            });
        }

        // Category filter
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        // Price range filter
        if ($this->priceRange) {
            switch ($this->priceRange) {
                case 'under_100k':
                    $query->where('base_price', '<', 100000);
                    break;
                case '100k_500k':
                    $query->whereBetween('base_price', [100000, 500000]);
                    break;
                case 'over_500k':
                    $query->where('base_price', '>', 500000);
                    break;
            }
        }

        // Sorting
        if ($this->userLatitude && $this->userLongitude && $this->sortBy === 'proximity') {
            // Calculate distance using Haversine formula
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
                    $query->orderBy('base_price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('base_price', 'desc');
                    break;
                case 'popular':
                    $query->withCount('likes')->orderBy('likes_count', 'desc');
                    break;
                default:
                    $query->latest();
            }
        }

        return $query->paginate(12);
    }

    public function getServicesProperty()
    {
        $query = Service::query()->with(['user', 'category', 'likes', 'bookmarks']);

        // Search filter
        if ($this->searchQuery) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('location_name', 'like', '%' . $this->searchQuery . '%');
            });
        }

        // Category filter
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        // Price range filter (for services, we'll use different ranges)
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

        // Sorting (similar to venues)
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
                default:
                    $query->latest();
            }
        }

        return $query->paginate(12);
    }

    public function getVenueCategoriesProperty()
    {
        return Category::where('type', 'venue')->get();
    }

    public function getServiceCategoriesProperty()
    {
        return Category::where('type', 'service')->get();
    }

    public function render()
    {
        $items = $this->viewType === 'venues' ? $this->venues : $this->services;
        $categories = $this->viewType === 'venues' ? $this->venueCategories : $this->serviceCategories;

        return view('livewire.client.homepage', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }
}