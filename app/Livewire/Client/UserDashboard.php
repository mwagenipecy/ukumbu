<?php

namespace App\Livewire\Client;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Venue;
use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class UserDashboard extends Component
{
    use WithPagination;

    public $activeTab = 'overview';
    public $selectedBooking = null;
    public $showBookingModal = false;

    protected $listeners = [
        'bookingUpdated' => '$refresh',
        'paymentProcessed' => '$refresh'
    ];

    public function mount()
    {
        $this->activeTab = 'overview';
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function showBookingDetails($bookingId)
    {
        $this->selectedBooking = Booking::with(['venue', 'user', 'payments'])->find($bookingId);
        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->selectedBooking = null;
    }

    public function cancelBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking && $booking->user_id === auth()->id()) {
            if ($booking->status === 'pending' || $booking->status === 'confirmed') {
                $booking->update(['status' => 'cancelled']);
                session()->flash('message', 'Booking cancelled successfully.');
                $this->dispatch('bookingUpdated');
            }
        }
    }

    public function getUpcomingBookingsProperty()
    {
        return auth()->user()->bookings()
            ->with(['venue', 'payments', 'bookingItems.service'])
            ->where('event_date', '>=', now()->format('Y-m-d'))
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('event_date')
            ->take(5)
            ->get();
    }

    public function getRecentBookingsProperty()
    {
        return auth()->user()->bookings()
            ->with(['venue', 'payments', 'bookingItems.service'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function getAllBookingsProperty()
    {
        return auth()->user()->bookings()
            ->with(['venue', 'payments', 'bookingItems.service'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getPaymentsProperty()
    {
        return auth()->user()->payments()
            ->with(['booking.venue'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getStatsProperty()
    {
        $user = auth()->user();
        
        return [
            'total_bookings' => $user->bookings()->count(),
            'upcoming_bookings' => $user->bookings()
                ->where('event_date', '>=', now()->format('Y-m-d'))
                ->whereIn('status', ['confirmed', 'pending'])
                ->count(),
            'completed_bookings' => $user->bookings()
                ->where('status', 'confirmed')
                ->where('event_date', '<', now()->format('Y-m-d'))
                ->count(),
            'cancelled_bookings' => $user->bookings()
                ->where('status', 'cancelled')
                ->count(),
            'total_spent' => $user->payments()
                ->where('payments.status', 'completed')
                ->sum('payments.amount'),
            'pending_payments' => $user->payments()
                ->where('payments.status', 'pending')
                ->count(),
            'favorite_venues' => $user->bookmarks()
                ->where('bookmarkable_type', Venue::class)
                ->count(),
            'favorite_services' => $user->bookmarks()
                ->where('bookmarkable_type', Service::class)
                ->count(),
        ];
    }

    public function getRecentActivityProperty()
    {
        $user = auth()->user();
        
        $activities = collect();
        
        // Add recent bookings
        $bookings = $user->bookings()
            ->with(['venue', 'bookingItems.service'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($booking) {
                // Determine if this is a venue booking or service booking
                if ($booking->venue) {
                    $title = 'Booked ' . $booking->venue->name;
                } elseif ($booking->bookingItems->count() > 0) {
                    $firstService = $booking->bookingItems->first()->service;
                    $title = 'Booked ' . ($firstService ? $firstService->name : 'Service');
                } else {
                    $title = 'Booking Created';
                }
                
                return [
                    'type' => 'booking',
                    'title' => $title,
                    'description' => 'Event on ' . Carbon::parse($booking->event_date)->format('M j, Y'),
                    'date' => $booking->created_at,
                    'status' => $booking->status,
                    'icon' => 'fas fa-calendar-check',
                    'color' => $this->getStatusColor($booking->status)
                ];
            });
        
        $activities = $activities->merge($bookings);
        
        // Add recent payments
        $payments = $user->payments()
            ->with(['booking.venue', 'booking.bookingItems.service'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($payment) {
                // Determine payment title based on booking type
                if ($payment->booking->venue) {
                    $title = 'Payment for ' . $payment->booking->venue->name;
                } elseif ($payment->booking->bookingItems->count() > 0) {
                    $firstService = $payment->booking->bookingItems->first()->service;
                    $title = 'Payment for ' . ($firstService ? $firstService->name : 'Service');
                } else {
                    $title = 'Payment Processed';
                }
                
                return [
                    'type' => 'payment',
                    'title' => $title,
                    'description' => 'Amount: TSh ' . number_format($payment->amount),
                    'date' => $payment->created_at,
                    'status' => $payment->status,
                    'icon' => 'fas fa-credit-card',
                    'color' => $this->getPaymentStatusColor($payment->status)
                ];
            });
        
        $activities = $activities->merge($payments);
        
        // Sort by date and take top 10
        return $activities->sortByDesc('date')->take(10);
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'confirmed' => 'text-green-600',
            'pending' => 'text-yellow-600',
            'cancelled' => 'text-red-600',
            'completed' => 'text-blue-600',
            default => 'text-gray-600'
        };
    }

    private function getPaymentStatusColor($status)
    {
        return match($status) {
            'completed' => 'text-green-600',
            'pending' => 'text-yellow-600',
            'failed' => 'text-red-600',
            'refunded' => 'text-blue-600',
            default => 'text-gray-600'
        };
    }

    public function render()
    {
        $stats = $this->stats;
        $upcomingBookings = $this->upcomingBookings;
        $recentActivity = $this->recentActivity;
        
        $data = [
            'stats' => $stats,
            'upcomingBookings' => $upcomingBookings,
            'recentActivity' => $recentActivity,
        ];

        if ($this->activeTab === 'bookings') {
            $data['bookings'] = $this->allBookings;
        } elseif ($this->activeTab === 'payments') {
            $data['payments'] = $this->payments;
        }

        return view('livewire.client.user-dashboard', $data);
    }
}
