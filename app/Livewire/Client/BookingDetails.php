<?php

namespace App\Livewire\Client;

use App\Models\Booking;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BookingDetails extends Component
{
    public $booking;
    public $showCancelModal = false;
    public $cancellationReason = '';

    public function mount($bookingId)
    {
        $this->booking = Booking::with([
            'venue',
            'user',
            'payments',
            'bookingItems.service'
        ])->where('id', $bookingId)
          ->where('user_id', Auth::id())
          ->firstOrFail();
    }

    public function openCancelModal()
    {
        if ($this->booking->status === 'cancelled') {
            session()->flash('error', 'This booking is already cancelled.');
            return;
        }

        if ($this->booking->status === 'completed') {
            session()->flash('error', 'Cannot cancel a completed booking.');
            return;
        }

        $this->showCancelModal = true;
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancellationReason = '';
    }

    public function cancelBooking()
    {
        $this->validate([
            'cancellationReason' => 'required|string|min:10|max:500'
        ], [
            'cancellationReason.required' => 'Please provide a reason for cancellation.',
            'cancellationReason.min' => 'Cancellation reason must be at least 10 characters.',
            'cancellationReason.max' => 'Cancellation reason cannot exceed 500 characters.'
        ]);

        try {
            $this->booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $this->cancellationReason,
                'cancelled_at' => now()
            ]);

            // Log the cancellation
            \Log::info('Booking cancelled', [
                'booking_id' => $this->booking->id,
                'user_id' => Auth::id(),
                'reason' => $this->cancellationReason
            ]);

            session()->flash('success', 'Your booking has been cancelled successfully. You will receive a confirmation email shortly.');
            
            $this->closeCancelModal();
            
            // Redirect back to dashboard after a short delay
            return redirect()->route('user.dashboard')->with('booking_cancelled', true);
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while cancelling your booking. Please try again or contact support.');
            \Log::error('Booking cancellation failed', [
                'booking_id' => $this->booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getBookingStatusColor($status)
    {
        return match($status) {
            'confirmed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPaymentStatusColor($status)
    {
        return match($status) {
            'paid' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function canCancel()
    {
        return in_array($this->booking->status, ['pending', 'confirmed']) && 
               $this->booking->event_date > now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.client.booking-details');
    }
}
