<?php

namespace App\Livewire\Client;

use App\Models\Service;
use App\Models\Review;
use App\Models\Booking;
use App\Models\BookingItem;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceDetail extends Component
{
    use WithPagination;

    public Service $service;
    public $activeTab = 'overview';
    
    // Booking form properties
    public $showBookingModal = false;
    public $eventDate = '';
    public $eventTime = '';
    public $quantity = 1;
    public $specialRequests = '';
    public $guestCount = '';
    
    // Review form properties
    public $showReviewModal = false;
    public $rating = 5;
    public $reviewComment = '';

    protected $queryString = [
        'activeTab' => ['except' => 'overview'],
    ];

    protected $rules = [
        'eventDate' => 'required|date|after:today',
        'eventTime' => 'required',
        'quantity' => 'required|integer|min:1',
        'guestCount' => 'nullable|integer|min:1',
        'specialRequests' => 'nullable|string|max:1000',
        'rating' => 'required|integer|min:1|max:5',
        'reviewComment' => 'nullable|string|max:500',
    ];

    public function mount(Service $service)
    {
        $this->service = $service->load(['user', 'category', 'reviews.user']);
        $this->eventDate = now()->addDays(1)->format('Y-m-d');
        $this->eventTime = '10:00';
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        if ($tab === 'reviews') {
            $this->resetPage();
        }
    }

    public function openBookingModal()
    {
        if (!auth()->check()) {
            session()->flash('error', 'Please login to book this service.');
            return redirect()->route('login');
        }
        
        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->reset(['eventDate', 'eventTime', 'quantity', 'specialRequests', 'guestCount']);
        $this->eventDate = now()->addDays(1)->format('Y-m-d');
        $this->eventTime = '10:00';
        $this->quantity = 1;
    }

    public function submitBooking()
    {
        $this->validate([
            'eventDate' => 'required|date|after:today',
            'eventTime' => 'required',
            'quantity' => 'required|integer|min:1',
            'guestCount' => 'nullable|integer|min:1',
            'specialRequests' => 'nullable|string|max:1000',
        ]);

        // Create booking
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'venue_id' => null, // Service booking, no venue required
            'event_date' => $this->eventDate,
            'event_time' => $this->eventTime,
            'guest_count' => $this->guestCount ?: 1,
            'status' => 'pending',
            'special_requests' => $this->specialRequests,
            'total_amount' => $this->service->pricing * $this->quantity,
        ]);

        // Create booking item for the service
        BookingItem::create([
            'booking_id' => $booking->id,
            'service_id' => $this->service->id,
            'quantity' => $this->quantity,
            'price' => $this->service->pricing * $this->quantity,
        ]);

        session()->flash('booking_success', 'Service booking request submitted successfully! You will receive a confirmation email shortly.');
        $this->closeBookingModal();
        
        return redirect()->route('user.dashboard');
    }

    public function openReviewModal()
    {
        if (!auth()->check()) {
            session()->flash('error', 'Please login to leave a review.');
            return redirect()->route('login');
        }
        
        // Check if user has booked this service
        $hasBooked = BookingItem::whereHas('booking', function($query) {
            $query->where('user_id', auth()->id())
                  ->where('status', 'confirmed');
        })->where('service_id', $this->service->id)->exists();
        
        if (!$hasBooked) {
            session()->flash('error', 'You can only review services you have booked.');
            return;
        }
        
        // Check if user has already reviewed
        $existingReview = Review::where('user_id', auth()->id())
                               ->where('reviewable_type', Service::class)
                               ->where('reviewable_id', $this->service->id)
                               ->first();
        
        if ($existingReview) {
            session()->flash('error', 'You have already reviewed this service.');
            return;
        }
        
        $this->showReviewModal = true;
    }

    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->reset(['rating', 'reviewComment']);
        $this->rating = 5;
    }

    public function submitReview()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'reviewComment' => 'nullable|string|max:500',
        ]);

        Review::create([
            'user_id' => auth()->id(),
            'reviewable_type' => Service::class,
            'reviewable_id' => $this->service->id,
            'rating' => $this->rating,
            'comment' => $this->reviewComment,
        ]);

        session()->flash('success', 'Your review has been submitted successfully!');
        $this->closeReviewModal();
        $this->service->refresh();
    }

    public function render()
    {
        $reviews = $this->service->reviews()
            ->with('user')
            ->latest()
            ->paginate(5);

        $averageRating = $this->service->reviews()->avg('rating') ?? 0;
        $totalReviews = $this->service->reviews()->count();
        
        $relatedServices = Service::where('category_id', $this->service->category_id)
                                ->where('id', '!=', $this->service->id)
                                ->where('status', 'active')
                                ->limit(3)
                                ->get();

        return view('livewire.client.service-detail', [
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
            'relatedServices' => $relatedServices,
        ]);
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

}

