<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public User $user;
    public $activeTab = 'overview';
    
    // Analytics data
    public $analyticsData = [];
    
    // Password reset
    public $showPasswordModal = false;
    public $newPassword = '';
    public $confirmPassword = '';

    protected $queryString = [
        'activeTab' => ['except' => 'overview'],
    ];

    public function mount(User $user)
    {
        $this->user = $user->load(['services', 'bookings', 'reviews']);
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        if ($this->user->role === 'vendor') {
            $this->analyticsData = [
                'total_services' => $this->user->services()->count(),
                'active_services' => $this->user->services()->where('status', 'active')->count(),
                'pending_services' => $this->user->services()->where('status', 'pending')->count(),
                'total_bookings' => $this->user->services()
                    ->join('booking_items', 'services.id', '=', 'booking_items.service_id')
                    ->count(),
                'total_revenue' => $this->user->services()
                    ->join('booking_items', 'services.id', '=', 'booking_items.service_id')
                    ->sum('booking_items.price'),
                'average_rating' => $this->user->services()
                    ->join('reviews', function($join) {
                        $join->on('services.id', '=', 'reviews.reviewable_id')
                             ->where('reviews.reviewable_type', '=', Service::class);
                    })
                    ->avg('reviews.rating') ?? 0,
                'this_month_bookings' => $this->user->services()
                    ->join('booking_items', 'services.id', '=', 'booking_items.service_id')
                    ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                    ->whereMonth('bookings.created_at', now()->month)
                    ->whereYear('bookings.created_at', now()->year)
                    ->count(),
            ];
        } elseif ($this->user->role === 'client') {
            $this->analyticsData = [
                'total_bookings' => $this->user->bookings()->count(),
                'confirmed_bookings' => $this->user->bookings()->where('status', 'confirmed')->count(),
                'pending_bookings' => $this->user->bookings()->where('status', 'pending')->count(),
                'cancelled_bookings' => $this->user->bookings()->where('status', 'cancelled')->count(),
                'total_spent' => $this->user->bookings()->sum('total_amount'),
                'upcoming_bookings' => $this->user->bookings()->where('event_date', '>=', now())->count(),
                'reviews_given' => $this->user->reviews()->count(),
                'average_rating_given' => $this->user->reviews()->avg('rating') ?? 0,
            ];
        } else {
            $this->analyticsData = [
                'system_access' => 'Full Admin Access',
                'last_login' => $this->user->updated_at,
            ];
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openPasswordModal()
    {
        $this->showPasswordModal = true;
        $this->newPassword = '';
        $this->confirmPassword = '';
    }

    public function closePasswordModal()
    {
        $this->showPasswordModal = false;
        $this->newPassword = '';
        $this->confirmPassword = '';
    }

    public function resetPassword()
    {
        $this->validate([
            'newPassword' => ['required', 'min:8', 'confirmed'],
            'confirmPassword' => ['required'],
        ], [
            'newPassword.confirmed' => 'The password confirmation does not match.',
        ], [
            'newPassword' => 'new password',
            'confirmPassword' => 'password confirmation',
        ]);

        $this->user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        session()->flash('success', 'Password updated successfully.');
        $this->closePasswordModal();
    }

    public function toggleEmailVerification()
    {
        if ($this->user->email_verified_at) {
            $this->user->update(['email_verified_at' => null]);
            session()->flash('success', 'Email verification removed.');
        } else {
            $this->user->update(['email_verified_at' => now()]);
            session()->flash('success', 'Email verified successfully.');
        }
        
        $this->user->refresh();
    }

    public function changeRole($newRole)
    {
        if ($this->user->id === auth()->id() && $newRole !== 'admin') {
            session()->flash('error', 'You cannot change your own admin role.');
            return;
        }

        $this->user->update(['role' => $newRole]);
        session()->flash('success', 'User role updated to ' . ucfirst($newRole) . ' successfully.');
        
        $this->user->refresh();
        $this->loadAnalytics();
    }

    public function deleteUser()
    {
        if ($this->user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $userName = $this->user->name;
        $this->user->delete();
        
        session()->flash('success', "User '{$userName}' has been deleted successfully.");
        
        return redirect()->route('admin.users.index');
    }

    public function impersonateUser()
    {
        if ($this->user->id === auth()->id()) {
            session()->flash('error', 'You cannot impersonate yourself.');
            return;
        }

        // Store original user ID in session for later restoration
        session(['impersonator' => auth()->id()]);
        auth()->login($this->user);
        
        return redirect()->route('dashboard')->with('success', "Now impersonating {$this->user->name}");
    }

    public function render()
    {
        $services = collect();
        $bookings = collect();
        $reviews = collect();

        if ($this->activeTab === 'services' && $this->user->role === 'vendor') {
            $services = $this->user->services()
                ->with(['category', 'bookingItems'])
                ->latest()
                ->paginate(10, ['*'], 'servicesPage');
        } elseif ($this->activeTab === 'bookings') {
            if ($this->user->role === 'client') {
                $bookings = $this->user->bookings()
                    ->with(['venue', 'bookingItems.service'])
                    ->latest()
                    ->paginate(10, ['*'], 'bookingsPage');
            } elseif ($this->user->role === 'vendor') {
                $bookings = Booking::whereHas('bookingItems.service', function($query) {
                        $query->where('user_id', $this->user->id);
                    })
                    ->with(['user', 'venue', 'bookingItems.service'])
                    ->latest()
                    ->paginate(10, ['*'], 'bookingsPage');
            }
        } elseif ($this->activeTab === 'reviews') {
            if ($this->user->role === 'client') {
                $reviews = $this->user->reviews()
                    ->with(['reviewable'])
                    ->latest()
                    ->paginate(10, ['*'], 'reviewsPage');
            } elseif ($this->user->role === 'vendor') {
                $reviews = Review::whereHasMorph('reviewable', [Service::class], function($query) {
                        $query->where('user_id', $this->user->id);
                    })
                    ->with(['user', 'reviewable'])
                    ->latest()
                    ->paginate(10, ['*'], 'reviewsPage');
            }
        }

        return view('livewire.admin.user.show', [
            'services' => $services,
            'bookings' => $bookings,
            'reviews' => $reviews,
        ]);
    }

    public function getRoleColor($role)
    {
        return match($role) {
            'admin' => 'bg-red-100 text-red-800',
            'vendor' => 'bg-blue-100 text-blue-800',
            'client' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getRoleIcon($role)
    {
        return match($role) {
            'admin' => 'fas fa-user-shield',
            'vendor' => 'fas fa-store',
            'client' => 'fas fa-user',
            default => 'fas fa-question-circle',
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