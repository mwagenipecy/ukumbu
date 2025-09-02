<?php

namespace App\Livewire\Vendor;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class BookingManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $selectedBooking = null;
    public $showBookingModal = false;
    public $confirmationNote = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function viewBooking($bookingId)
    {
        $this->selectedBooking = Booking::with(['user', 'venue', 'bookingItems.service', 'payments'])
                                        ->findOrFail($bookingId);
        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->selectedBooking = null;
        $this->confirmationNote = '';
    }

    public function confirmBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        // Ensure this booking belongs to vendor's venue
        if ($booking->venue->user_id !== auth()->id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $booking->update(['status' => 'confirmed']);
        
        session()->flash('success', 'Booking confirmed successfully!');
        $this->closeBookingModal();
    }

    public function cancelBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        // Ensure this booking belongs to vendor's venue
        if ($booking->venue->user_id !== auth()->id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $booking->update(['status' => 'cancelled']);
        
        session()->flash('success', 'Booking cancelled successfully!');
        $this->closeBookingModal();
    }

    public function getBookingsProperty()
    {
        $query = Booking::with(['user', 'venue', 'bookingItems.service'])
                       ->whereHas('venue', function($q) {
                           $q->where('user_id', auth()->id());
                       });

        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('venue', function($venueQuery) {
                    $venueQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFilter) {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('event_date', today());
                    break;
                case 'this_week':
                    $query->whereBetween('event_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('event_date', now()->month)
                          ->whereYear('event_date', now()->year);
                    break;
                case 'upcoming':
                    $query->where('event_date', '>=', today());
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    public function render()
    {
        return view('livewire.vendor.booking-management', [
            'bookings' => $this->getBookingsProperty(),
        ]);
    }
}
