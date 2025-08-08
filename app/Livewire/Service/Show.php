<?php

namespace App\Livewire\Service;

use App\Models\Service;
use App\Models\Review;
use App\Models\BookingItem;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Service $service;
    public $activeTab = 'overview';
    
    // Analytics data
    public $analyticsData = [];
    public $revenueData = [];
    
    // Status change confirmation
    public $showStatusModal = false;
    public $newStatus = '';
    public $statusChangeReason = '';

    protected $queryString = [
        'activeTab' => ['except' => 'overview'],
    ];

    public function mount(Service $service)
    {
        $this->service = $service->load(['user', 'category', 'reviews.user', 'bookingItems.booking']);
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        // Basic analytics
        $this->analyticsData = [
            'total_bookings' => $this->service->bookingItems()->count(),
            'total_revenue' => $this->service->bookingItems()->sum('price'),
            'average_rating' => $this->service->reviews()->avg('rating') ?? 0,
            'total_reviews' => $this->service->reviews()->count(),
            'this_month_bookings' => $this->service->bookingItems()
                ->whereHas('booking', function($query) {
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                })->count(),
            'last_month_bookings' => $this->service->bookingItems()
                ->whereHas('booking', function($query) {
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                })->count(),
        ];

        // Revenue data for chart (last 6 months)
        $this->revenueData = collect(range(5, 0))->map(function($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $revenue = $this->service->bookingItems()
                ->whereHas('booking', function($query) use ($date) {
                    $query->whereMonth('created_at', $date->month)
                          ->whereYear('created_at', $date->year);
                })
                ->sum('price');
            
            return [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ];
        })->toArray();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        if (in_array($tab, ['reviews', 'bookings'])) {
            $this->resetPage();
        }
    }

    public function openStatusModal($status)
    {
        $this->newStatus = $status;
        $this->showStatusModal = true;
        $this->statusChangeReason = '';
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->newStatus = '';
        $this->statusChangeReason = '';
    }

    public function changeStatus()
    {
        $this->validate([
            'statusChangeReason' => 'required|string|max:500',
        ]);

        $this->service->update([
            'status' => $this->newStatus,
        ]);

        // Here you could also log the status change with reason
        // StatusChangeLog::create([
        //     'service_id' => $this->service->id,
        //     'old_status' => $this->service->getOriginal('status'),
        //     'new_status' => $this->newStatus,
        //     'reason' => $this->statusChangeReason,
        //     'changed_by' => auth()->id(),
        // ]);

        session()->flash('success', 'Service status updated successfully.');
        
        $this->closeStatusModal();
        $this->service->refresh();
    }

    public function deleteService()
    {
        if (!$this->service->canBeDeleted()) {
            session()->flash('error', 'Cannot delete service with existing bookings.');
            return;
        }

        $serviceName = $this->service->name;
        $this->service->delete();
        
        session()->flash('success', "Service '{$serviceName}' has been deleted successfully.");
        
        return redirect()->route('admin.services.index');
    }

    public function contactProvider()
    {
        return redirect()->away('mailto:' . $this->service->user->email);
    }

    public function render()
    {
        $reviews = $this->service->reviews()
            ->with('user')
            ->latest()
            ->paginate(10, ['*'], 'reviewsPage');

        $bookings = $this->service->bookingItems()
            ->with(['booking.user', 'booking.venue'])
            ->latest()
            ->paginate(10, ['*'], 'bookingsPage');

        return view('livewire.service.show', [
            'reviews' => $reviews,
            'bookings' => $bookings,
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

    public function getStatusActions($currentStatus)
    {
        return match($currentStatus) {
            'pending' => ['active' => 'Approve', 'rejected' => 'Reject'],
            'active' => ['suspended' => 'Suspend'],
            'suspended' => ['active' => 'Reactivate', 'rejected' => 'Reject'],
            'rejected' => ['pending' => 'Move to Pending', 'active' => 'Approve'],
            default => [],
        };
    }

    public function getGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }
}