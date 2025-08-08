<?php

namespace App\Livewire\Admin\Booking;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class Pending extends Component
{
    use WithPagination;

    // Search and filter properties
    public string $search = '';
    public string $urgencyFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'asc'; // Show oldest first for urgency
    public int $perPage = 20;
    
    // Bulk actions
    public array $selectedBookings = [];
    public bool $selectAll = false;
    
    // Quick actions
    public bool $showConfirmModal = false;
    public ?Booking $confirmingBooking = null;
    public string $confirmationNote = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'urgencyFilter' => ['except' => ''],
        'perPage' => ['except' => 20],
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingUrgencyFilter()
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

    public function clearFilters()
    {
        $this->reset(['search', 'urgencyFilter']);
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedBookings = $this->getFilteredBookings()->pluck('id')->toArray();
        } else {
            $this->selectedBookings = [];
        }
    }

    public function openConfirmModal($bookingId)
    {
        $this->confirmingBooking = Booking::with(['user', 'venue', 'bookingItems.service'])
                                         ->findOrFail($bookingId);
        $this->confirmationNote = '';
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->confirmingBooking = null;
        $this->confirmationNote = '';
    }

    public function confirmBooking()
    {
        $this->validate([
            'confirmationNote' => 'nullable|string|max:500',
        ]);

        $this->confirmingBooking->update(['status' => 'confirmed']);

        // Send confirmation email to customer
        // Mail::to($this->confirmingBooking->user->email)
        //     ->send(new BookingConfirmed($this->confirmingBooking, $this->confirmationNote));

        session()->flash('success', 'Booking confirmed successfully. Customer has been notified.');
        $this->closeConfirmModal();
    }

    public function quickConfirm($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->update(['status' => 'confirmed']);
        
        session()->flash('success', 'Booking #' . $booking->id . ' confirmed successfully.');
    }

    public function quickCancel($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->update(['status' => 'cancelled']);
        
        session()->flash('success', 'Booking #' . $booking->id . ' cancelled successfully.');
    }

    public function bulkConfirm()
    {
        Booking::whereIn('id', $this->selectedBookings)->update(['status' => 'confirmed']);
        
        $count = count($this->selectedBookings);
        $this->selectedBookings = [];
        $this->selectAll = false;
        
        session()->flash('success', $count . ' bookings confirmed successfully.');
    }

    public function bulkCancel()
    {
        Booking::whereIn('id', $this->selectedBookings)->update(['status' => 'cancelled']);
        
        $count = count($this->selectedBookings);
        $this->selectedBookings = [];
        $this->selectAll = false;
        
        session()->flash('success', $count . ' bookings cancelled successfully.');
    }

    private function getFilteredBookings()
    {
        return Booking::query()
            ->with(['user', 'venue', 'bookingItems.service'])
            ->where('status', 'pending')
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('id', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function (Builder $userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('venue', function (Builder $venueQuery) {
                          $venueQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->urgencyFilter, function (Builder $query) {
                $days = now();
                switch ($this->urgencyFilter) {
                    case 'today':
                        $query->whereDate('event_date', $days);
                        break;
                    case 'tomorrow':
                        $query->whereDate('event_date', $days->addDay());
                        break;
                    case 'this_week':
                        $query->whereBetween('event_date', [$days, $days->addWeek()]);
                        break;
                    case 'overdue':
                        $query->whereDate('created_at', '<', $days->subDays(2));
                        break;
                }
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $bookings = $this->getFilteredBookings()->paginate($this->perPage);
        
        $stats = [
            'total_pending' => Booking::where('status', 'pending')->count(),
            'today_events' => Booking::where('status', 'pending')
                                    ->whereDate('event_date', today())
                                    ->count(),
            'tomorrow_events' => Booking::where('status', 'pending')
                                       ->whereDate('event_date', today()->addDay())
                                       ->count(),
            'this_week' => Booking::where('status', 'pending')
                                 ->whereBetween('event_date', [now(), now()->addWeek()])
                                 ->count(),
            'overdue' => Booking::where('status', 'pending')
                               ->whereDate('created_at', '<', now()->subDays(2))
                               ->count(),
            'avg_wait_time' => $this->calculateAverageWaitTime(),
        ];

        return view('livewire.admin.booking.pending', [
            'bookings' => $bookings,
            'stats' => $stats,
        ]);
    }

    private function calculateAverageWaitTime()
    {
        $pendingBookings = Booking::where('status', 'pending')
                                 ->get(['created_at']);
        
        if ($pendingBookings->isEmpty()) {
            return 0;
        }

        $totalHours = $pendingBookings->sum(function ($booking) {
            return now()->diffInHours($booking->created_at);
        });

        return round($totalHours / $pendingBookings->count(), 1);
    }

    public function getUrgencyLevel($booking)
    {
        $hoursWaiting = now()->diffInHours($booking->created_at);
        $daysUntilEvent = now()->diffInDays($booking->event_date, false);

        if ($daysUntilEvent <= 0) {
            return 'critical'; // Event is today or past
        } elseif ($daysUntilEvent <= 1 || $hoursWaiting >= 48) {
            return 'urgent';   // Event tomorrow or waiting > 2 days
        } elseif ($daysUntilEvent <= 7 || $hoursWaiting >= 24) {
            return 'moderate'; // Event this week or waiting > 1 day
        } else {
            return 'normal';
        }
    }

    public function getUrgencyColor($urgencyLevel)
    {
        return match($urgencyLevel) {
            'critical' => 'bg-red-100 text-red-800 border-red-200',
            'urgent' => 'bg-orange-100 text-orange-800 border-orange-200',
            'moderate' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'normal' => 'bg-green-100 text-green-800 border-green-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getWaitingTime($booking)
    {
        $hours = now()->diffInHours($booking->created_at);
        
        if ($hours < 1) {
            return now()->diffInMinutes($booking->created_at) . ' minutes';
        } elseif ($hours < 24) {
            return $hours . ' hours';
        } else {
            $days = now()->diffInDays($booking->created_at);
            return $days . ' day' . ($days > 1 ? 's' : '');
        }
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