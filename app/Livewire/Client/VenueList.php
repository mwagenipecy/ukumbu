<?php

namespace App\Livewire\Client;

use App\Models\Venue;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class VenueList extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $priceRange = '';
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

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function getVenuesProperty()
    {
        $query = Venue::with(['user', 'category', 'likes', 'bookmarks'])
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
                case 'latest':
                default:
                    $query->latest();
            }
        }

        return $query->paginate(12);
    }

    public function getCategoriesProperty()
    {
        return Category::where('type', 'venue')->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.client.venue-list', [
            'venues' => $this->venues,
            'categories' => $this->categories,
        ])->layout('layouts.app');
    }
}

