<?php

namespace App\Livewire\Vendor;

use App\Models\Service;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class ServiceManagement extends Component
{
    use WithPagination;

    // Search and filter properties
    public string $search = '';
    public string $categoryFilter = '';
    public string $statusFilter = '';
    public string $pricingModelFilter = '';
    public int $perPage = 10;
    
    // Sorting
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'pricingModelFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPricingModelFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'categoryFilter', 'statusFilter', 'pricingModelFilter']);
        $this->resetPage();
    }

    public function editService($serviceId)
    {
        return redirect()->route('service.form.edit', $serviceId);
    }

    public function deleteService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        // Ensure this service belongs to the vendor
        if ($service->user_id !== auth()->id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $serviceName = $service->name;
        
        // Check if service has any bookings
        if ($service->bookingItems()->exists()) {
            session()->flash('error', "Cannot delete '{$serviceName}' because it has existing bookings.");
            return;
        }
        
        $service->delete();
        
        session()->flash('success', "Service '{$serviceName}' has been deleted successfully.");
    }

    public function duplicateService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        // Ensure this service belongs to the vendor
        if ($service->user_id !== auth()->id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $newService = $service->replicate();
        $newService->name = $service->name . ' (Copy)';
        $newService->status = 'pending';
        $newService->save();
        
        session()->flash('success', "Service '{$service->name}' has been duplicated successfully.");
    }

    private function getFilteredServices()
    {
        return Service::query()
            ->with(['category'])
            ->where('user_id', auth()->id()) // Only show vendor's own services
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('location_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, function (Builder $query) {
                $query->whereHas('category', function (Builder $categoryQuery) {
                    $categoryQuery->where('name', $this->categoryFilter);
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->pricingModelFilter, function (Builder $query) {
                $query->where('pricing_model', $this->pricingModelFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $services = $this->getFilteredServices()->paginate($this->perPage);
        
        $categories = Category::where('type', 'service')->get();
        
        // Stats for vendor's services only
        $stats = [
            'total' => Service::where('user_id', auth()->id())->count(),
            'active' => Service::where('user_id', auth()->id())->where('status', 'active')->count(),
            'pending' => Service::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'suspended' => Service::where('user_id', auth()->id())->where('status', 'suspended')->count(),
            'rejected' => Service::where('user_id', auth()->id())->where('status', 'rejected')->count(),
        ];

        return view('livewire.vendor.service-management', [
            'services' => $services,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'active' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'suspended' => 'bg-orange-100 text-orange-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusIcon($status)
    {
        return match($status) {
            'active' => 'fas fa-check-circle text-green-500',
            'pending' => 'fas fa-clock text-yellow-500',
            'suspended' => 'fas fa-pause-circle text-orange-500',
            'rejected' => 'fas fa-times-circle text-red-500',
            default => 'fas fa-question-circle text-gray-500',
        };
    }

    public function getCategoryColor($categoryName)
    {
        return match(strtolower($categoryName)) {
            'catering' => 'bg-green-100 text-green-800',
            'photography' => 'bg-purple-100 text-purple-800',
            'decoration' => 'bg-pink-100 text-pink-800',
            'music & dj' => 'bg-blue-100 text-blue-800',
            'security' => 'bg-gray-100 text-gray-800',
            'transport' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-indigo-100 text-indigo-800',
        };
    }

    public function formatPrice($price, $pricingModel)
    {
        $formattedPrice = 'TSh ' . number_format($price);
        
        return match($pricingModel) {
            'flat' => $formattedPrice . ' (Flat rate)',
            'hourly' => $formattedPrice . ' (Per hour)',
            'per_guest' => $formattedPrice . ' (Per guest)',
            default => $formattedPrice,
        };
    }
}

