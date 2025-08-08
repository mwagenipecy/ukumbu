<?php

namespace App\Livewire\Admin\Booking;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use App\Models\Venue;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class Index extends Component
{
    use WithPagination;

    // Search and filter properties
    public string $search = '';
    public string $statusFilter = '';
    public string $paymentStatusFilter = '';
    public string $dateRangeFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 15;
    
    // Custom date filters
    public string $startDate = '';
    public string $endDate = '';
    
    // Bulk actions
    public array $selectedBookings = [];
    public bool $selectAll = false;
    public bool $showBulkModal = false;
    
    // Booking management
    public bool $showBookingModal = false;
    public ?Booking $managingBooking = null;
    public string $managementAction = '';
    public string $managementReason = '';
    public string $refundAmount = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'paymentStatusFilter' => ['except' => ''],
        'dateRangeFilter' => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function mount()
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateRangeFilter()
    {
        $this->setDateRange();
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

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'paymentStatusFilter', 'dateRangeFilter']);
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function setDateRange()
    {
        switch ($this->dateRangeFilter) {
            case 'today':
                $this->startDate = now()->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
                break;
            case 'week':
                $this->startDate = now()->startOfWeek()->format('Y-m-d');
                $this->endDate = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = now()->startOfYear()->format('Y-m-d');
                $this->endDate = now()->endOfYear()->format('Y-m-d');
                break;
        }
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedBookings = $this->getFilteredBookings()->pluck('id')->toArray();
        } else {
            $this->selectedBookings = [];
        }
    }

    public function openBookingModal($bookingId, $action)
    {
        $this->managingBooking = Booking::with(['user', 'venue', 'bookingItems.service', 'payments'])
                                      ->findOrFail($bookingId);
        $this->managementAction = $action;
        $this->managementReason = '';
        $this->refundAmount = '';
        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->managingBooking = null;
        $this->managementAction = '';
        $this->managementReason = '';
        $this->refundAmount = '';
    }

    public function manageBooking()
    {
        $rules = [
            'managementReason' => 'required|string|max:500',
        ];

        if ($this->managementAction === 'refund') {
            $rules['refundAmount'] = 'required|numeric|min:0|max:' . $this->managingBooking->total_amount;
        }

        $this->validate($rules);

        switch ($this->managementAction) {
            case 'confirm':
                $this->managingBooking->update(['status' => 'confirmed']);
                $message = 'Booking confirmed successfully.';
                break;
            case 'cancel':
                $this->managingBooking->update(['status' => 'cancelled']);
                $message = 'Booking cancelled successfully.';
                break;
            case 'refund':
                // Here you would integrate with payment gateway for refund
                $this->managingBooking->update([
                    'status' => 'cancelled',
                    'payment_status' => 'refunded'
                ]);
                $message = 'Booking cancelled and refund processed.';
                break;
            case 'payment_resolved':
                $this->managingBooking->update(['payment_status' => 'paid']);
                $message = 'Payment status updated successfully.';
                break;
            default:
                $message = 'Action completed.';
        }

        // Log the action
        // BookingLog::create([
        //     'booking_id' => $this->managingBooking->id,
        //     'action' => $this->managementAction,
        //     'reason' => $this->managementReason,
        //     'refund_amount' => $this->refundAmount ?: null,
        //     'managed_by' => auth()->id(),
        // ]);

        session()->flash('success', $message);
        $this->closeBookingModal();
    }

    public function toggleBulkModal()
    {
        $this->showBulkModal = !$this->showBulkModal;
    }

    public function bulkConfirm()
    {
        Booking::whereIn('id', $this->selectedBookings)->update(['status' => 'confirmed']);
        
        $this->selectedBookings = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected bookings confirmed successfully.');
    }

    public function bulkCancel()
    {
        Booking::whereIn('id', $this->selectedBookings)->update(['status' => 'cancelled']);
        
        $this->selectedBookings = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected bookings cancelled successfully.');
    }

    public function bulkUpdatePayment($status)
    {
        Booking::whereIn('id', $this->selectedBookings)->update(['payment_status' => $status]);
        
        $this->selectedBookings = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected bookings payment status updated successfully.');
    }

    public function quickConfirm($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->update(['status' => 'confirmed']);
        
        session()->flash('success', 'Booking confirmed successfully.');
    }

    public function quickCancel($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->update(['status' => 'cancelled']);
        
        session()->flash('success', 'Booking cancelled successfully.');
    }

    private function getFilteredBookings()
    {
        return Booking::query()
            ->with(['user', 'venue', 'bookingItems.service', 'payments'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('id', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function (Builder $userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('venue', function (Builder $venueQuery) {
                          $venueQuery->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('bookingItems.service', function (Builder $serviceQuery) {
                          $serviceQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->paymentStatusFilter, function (Builder $query) {
                $query->where('payment_status', $this->paymentStatusFilter);
            })
            ->when($this->startDate && $this->endDate, function (Builder $query) {
                $query->whereBetween('event_date', [$this->startDate, $this->endDate]);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $bookings = $this->getFilteredBookings()->paginate($this->perPage);
        
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'payment_pending' => Booking::where('payment_status', 'unpaid')->count(),
            'payment_issues' => Booking::whereIn('payment_status', ['failed', 'disputed'])->count(),
            'total_revenue' => Booking::where('status', 'confirmed')->sum('total_amount'),
            'this_month' => Booking::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count(),
        ];

        return view('livewire.admin.booking.index', [
            'bookings' => $bookings,
            'stats' => $stats,
        ]);
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'confirmed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentStatusColor($paymentStatus)
    {
        return match($paymentStatus) {
            'paid' => 'bg-green-100 text-green-800',
            'unpaid' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-blue-100 text-blue-800',
            'disputed' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
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

    public function isUpcoming($eventDate)
    {
        return now()->lt($eventDate);
    }

    public function isPast($eventDate)
    {
        return now()->gt($eventDate);
    }

    public function getDaysUntilEvent($eventDate)
    {
        return now()->diffInDays($eventDate, false);
    }
}