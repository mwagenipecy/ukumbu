<?php

namespace App\Livewire\Admin\Service;

use App\Models\Service;
use App\Models\User;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class Index extends Component
{
    use WithPagination;

    // Search and filter properties
    public string $search = '';
    public string $categoryFilter = '';
    public string $statusFilter = '';
    public string $pricingModelFilter = '';
    public int $perPage = 10;
    
    // Bulk actions
    public array $selectedServices = [];
    public bool $selectAll = false;
    public bool $showBulkModal = false;
    
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

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedServices = $this->getFilteredServices()->pluck('id')->toArray();
        } else {
            $this->selectedServices = [];
        }
    }

    public function toggleBulkModal()
    {
        $this->showBulkModal = !$this->showBulkModal;
    }

    public function bulkApprove()
    {
        Service::whereIn('id', $this->selectedServices)->update(['status' => 'active']);
        
        $this->selectedServices = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected services have been approved successfully.');
    }

    public function bulkReject()
    {
        Service::whereIn('id', $this->selectedServices)->update(['status' => 'rejected']);
        
        $this->selectedServices = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected services have been rejected successfully.');
    }

    public function bulkSuspend()
    {
        Service::whereIn('id', $this->selectedServices)->update(['status' => 'suspended']);
        
        $this->selectedServices = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected services have been suspended successfully.');
    }

    public function approveService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->update(['status' => 'active']);
        
        session()->flash('success', "Service '{$service->name}' has been approved successfully.");
    }

    public function rejectService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->update(['status' => 'rejected']);
        
        session()->flash('success', "Service '{$service->name}' has been rejected successfully.");
    }

    public function suspendService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->update(['status' => 'suspended']);
        
        session()->flash('success', "Service '{$service->name}' has been suspended successfully.");
    }

    public function deleteService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $serviceName = $service->name;
        
        // Check if service has any bookings
        if ($service->bookingItems()->exists()) {
            session()->flash('error', "Cannot delete '{$serviceName}' because it has existing bookings.");
            return;
        }
        
        $service->delete();
        
        session()->flash('success', "Service '{$serviceName}' has been deleted successfully.");
    }

    private function getFilteredServices()
    {
        return Service::query()
            ->with(['user', 'category'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('location_name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function (Builder $userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
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
        
        $stats = [
            'total' => Service::count(),
            'active' => Service::where('status', 'active')->count(),
            'pending' => Service::where('status', 'pending')->count(),
            'suspended' => Service::where('status', 'suspended')->count(),
            'rejected' => Service::where('status', 'rejected')->count(),
        ];

        return view('livewire.admin.service.index', [
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

    public function getUserInitials($name)
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    public function getUserAvatarColor($userId)
    {
        $colors = [
            'bg-blue-100 text-blue-600',
            'bg-purple-100 text-purple-600',
            'bg-green-100 text-green-600',
            'bg-yellow-100 text-yellow-600',
            'bg-pink-100 text-pink-600',
            'bg-indigo-100 text-indigo-600',
        ];
        
        return $colors[$userId % count($colors)];
    }
}