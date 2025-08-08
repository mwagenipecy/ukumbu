<?php

namespace App\Livewire\Client;

use App\Models\Venue;
use App\Models\Review;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class VenueDetail extends Component
{
    public $venue;
    public $selectedImageIndex = 0;
    public $showImageModal = false;
    public $selectedDate = null;
    public $selectedServices = [];
    public $showBookingModal = false;
    public $reviewRating = 5;
    public $reviewComment = '';
    public $showReviewForm = false;

    protected $listeners = [
        'likeToggled' => '$refresh',
        'bookmarkToggled' => '$refresh',
        'serviceToggled' => 'toggleService'
    ];

    public function mount($venueId)
    {

        $this->venue = Venue::with([
            'user',
            'category',
            'services.category',
            'likes',
            'bookmarks',
            'reviews.user'
        ])->findOrFail($venueId);



    }

    public function selectImage($index)
    {
        $this->selectedImageIndex = $index;
    }

    public function openImageModal($index = null)
    {
        if ($index !== null) {
            $this->selectedImageIndex = $index;
        }
        $this->showImageModal = true;
    }

    public function closeImageModal()
    {
        $this->showImageModal = false;
    }

    public function nextImage()
    {
        $gallery = $this->venue->gallery ?? [];
        if (count($gallery) > 0) {
            $this->selectedImageIndex = ($this->selectedImageIndex + 1) % count($gallery);
        }
    }

    public function previousImage()
    {
        $gallery = $this->venue->gallery ?? [];
        if (count($gallery) > 0) {
            $this->selectedImageIndex = ($this->selectedImageIndex - 1 + count($gallery)) % count($gallery);
        }
    }

    public function toggleService($serviceId)
    {
        if (in_array($serviceId, $this->selectedServices)) {
            $this->selectedServices = array_filter($this->selectedServices, function($id) use ($serviceId) {
                return $id != $serviceId;
            });
        } else {
            $this->selectedServices[] = $serviceId;
        }
    }

    public function openBookingModal()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->selectedDate = null;
        $this->selectedServices = [];
    }

    public function submitBooking()
    {
        $this->validate([
            'selectedDate' => 'required|date|after:today',
        ]);

        // Calculate total amount
        $totalAmount = $this->venue->base_price;
        foreach ($this->selectedServices as $serviceId) {
            $service = $this->venue->services->find($serviceId);
            if ($service) {
                $totalAmount += $service->price;
            }
        }

        // Create booking (simplified - in real app, you'd handle this differently)
        $booking = $this->venue->bookings()->create([
            'user_id' => Auth::id(),
            'event_date' => $this->selectedDate,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        // Add service items
        foreach ($this->selectedServices as $serviceId) {
            $service = $this->venue->services->find($serviceId);
            if ($service) {
                $booking->bookingItems()->create([
                    'service_id' => $serviceId,
                    'quantity' => 1,
                    'price' => $service->price,
                ]);
            }
        }

        // Redirect to booking confirmation or payment
        session()->flash('booking_success', 'Booking request submitted successfully!');
        return redirect()->route('bookings.show', $booking->id);
    }

    public function submitReview()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'reviewRating' => 'required|integer|min:1|max:5',
            'reviewComment' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this venue
        $existingReview = $this->venue->reviews()->where('user_id', Auth::id())->first();
        
        if ($existingReview) {
            $existingReview->update([
                'rating' => $this->reviewRating,
                'comment' => $this->reviewComment,
            ]);
            session()->flash('review_success', 'Review updated successfully!');
        } else {
            $this->venue->reviews()->create([
                'user_id' => Auth::id(),
                'rating' => $this->reviewRating,
                'comment' => $this->reviewComment,
            ]);
            session()->flash('review_success', 'Review submitted successfully!');
        }

        $this->showReviewForm = false;
        $this->reviewRating = 5;
        $this->reviewComment = '';
        $this->dispatch('$refresh');
    }

    public function toggleReviewForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->showReviewForm = !$this->showReviewForm;
        
        // If opening form and user has existing review, load it
        if ($this->showReviewForm) {
            $existingReview = $this->venue->reviews()->where('user_id', Auth::id())->first();
            if ($existingReview) {
                $this->reviewRating = $existingReview->rating;
                $this->reviewComment = $existingReview->comment;
            }
        }
    }

    public function render()
    {
        return view('livewire.client.venue-detail', [
            'averageRating' => $this->venue->reviews()->avg('rating') ?? 0,
            'totalReviews' => $this->venue->reviews()->count(),
            'userReview' => Auth::check() ? $this->venue->reviews()->where('user_id', Auth::id())->first() : null,
        ]);
    }
}