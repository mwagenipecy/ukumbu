<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\Venue;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Calendar extends Component
{
    use WithPagination;

    public $selectedDate;
    public $selectedVenue;
    public $view = 'month'; // month, week, day
    public $venues;
    public $bookings = [];
    public $showBookingModal = false;
    public $selectedBooking;

    public $view="month";   

    protected $queryString = [
        'selectedDate' => ['except' => ''],
        'selectedVenue' => ['except' => ''],
        'view' => ['except' => 'month']
    ];

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->venues = Venue::where('status', 'active')->get();
        $this->loadBookings();
    }

    public function loadBookings()
    {
        $query = Booking::with(['user', 'venue']);

        if ($this->selectedVenue) {
            $query->where('venue_id', $this->selectedVenue);
        }

        if ($this->view === 'month') {
            $startDate = Carbon::parse($this->selectedDate)->startOfMonth();
            $endDate = Carbon::parse($this->selectedDate)->endOfMonth();
            $query->whereBetween('event_date', [$startDate, $endDate]);
        } elseif ($this->view === 'week') {
            $startDate = Carbon::parse($this->selectedDate)->startOfWeek();
            $endDate = Carbon::parse($this->selectedDate)->endOfWeek();
            $query->whereBetween('event_date', [$startDate, $endDate]);
        } else { // day view
            $query->where('event_date', $this->selectedDate);
        }

        $this->bookings = $query->orderBy('event_date')->get();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadBookings();
    }

    public function selectVenue($venueId)
    {
        $this->selectedVenue = $venueId;
        $this->loadBookings();
    }

    public function changeView($view)
    {
        $this->view = $view;
        $this->loadBookings();
    }

    public function previousPeriod()
    {
        if ($this->view === 'month') {
            $this->selectedDate = Carbon::parse($this->selectedDate)->subMonth()->format('Y-m-d');
        } elseif ($this->view === 'week') {
            $this->selectedDate = Carbon::parse($this->selectedDate)->subWeek()->format('Y-m-d');
        } else {
            $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->format('Y-m-d');
        }
        $this->loadBookings();
    }

    public function nextPeriod()
    {
        if ($this->view === 'month') {
            $this->selectedDate = Carbon::parse($this->selectedDate)->addMonth()->format('Y-m-d');
        } elseif ($this->view === 'week') {
            $this->selectedDate = Carbon::parse($this->selectedDate)->addWeek()->format('Y-m-d');
        } else {
            $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->format('Y-m-d');
        }
        $this->loadBookings();
    }

    public function showBooking($bookingId)
    {
        $this->selectedBooking = Booking::with(['user', 'venue'])->find($bookingId);
        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->selectedBooking = null;
    }

    public function approveBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking) {
            $booking->update(['status' => 'confirmed']);
            $this->loadBookings();
            session()->flash('message', 'Booking approved successfully.');
        }
    }

    public function rejectBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking) {
            $booking->update(['status' => 'rejected']);
            $this->loadBookings();
            session()->flash('message', 'Booking rejected successfully.');
        }
    }

    public function checkAvailability($venueId, $date)
    {
        $existingBooking = Booking::where('venue_id', $venueId)
            ->where('event_date', $date)
            ->whereIn('status', ['confirmed', 'pending'])
            ->first();

        return $existingBooking ? false : true;
    }

    public function getAvailabilityColor($venueId, $date)
    {
        if (!$this->checkAvailability($venueId, $date)) {
            return 'bg-red-100 text-red-800'; // Booked
        }
        return 'bg-green-100 text-green-800'; // Available
    }

    public function getMonthDays()
    {
        $date = Carbon::parse($this->selectedDate);
        $start = $date->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $end = $date->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        
        $days = [];
        $current = $start->copy();
        
        while ($current <= $end) {
            $days[] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->day,
                'isCurrentMonth' => $current->month === $date->month,
                'isToday' => $current->isToday(),
                'bookings' => $this->getBookingsForDate($current->format('Y-m-d'))
            ];
            $current->addDay();
        }
        
        return $days;
    }

    public function getBookingsForDate($date)
    {
        return $this->bookings->where('event_date', $date);
    }

    public function getWeekDays()
    {
        $date = Carbon::parse($this->selectedDate);
        $start = $date->copy()->startOfWeek();
        $days = [];
        
        for ($i = 0; $i < 7; $i++) {
            $currentDate = $start->copy()->addDays($i);
            $days[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->format('D'),
                'fullDate' => $currentDate->format('M j'),
                'isToday' => $currentDate->isToday(),
                'bookings' => $this->getBookingsForDate($currentDate->format('Y-m-d'))
            ];
        }
        
        return $days;
    }

    public function render()
    {
        $this->loadBookings();
        
        $data = [
            'venues' => $this->venues,
            'bookings' => $this->bookings,
            'selectedDate' => $this->selectedDate,
            'view' => $this->view
        ];

        if ($this->view === 'month') {
            $data['monthDays'] = $this->getMonthDays();
        } elseif ($this->view === 'week') {
            $data['weekDays'] = $this->getWeekDays();
        }

        return view('livewire.admin.calendar', $data);
    }
}

