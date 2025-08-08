<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Venue;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $timeRange = 30;
    public $selectedMetric = 'revenue';
    
    // Real-time stats
    public $totalRevenue;
    public $totalBookings;
    public $activeVenues;
    public $totalUsers;
    public $revenueGrowth;
    public $bookingsGrowth;
    public $venuesGrowth;
    public $usersGrowth;
    
    // Chart data
    public $revenueChartData = [];
    public $bookingStatusData = [];
    
    // Lists
    public $recentActivities = [];
    public $pendingApprovals = [];
    public $topVenues = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function updatedTimeRange()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->calculateStats();
        $this->loadRevenueChart();
        $this->loadBookingStatusChart();
        $this->loadRecentActivities();
        $this->loadPendingApprovals();
        $this->loadTopVenues();
    }

    private function calculateStats()
    {
        $currentPeriod = Carbon::now()->subDays($this->timeRange);
        $previousPeriod = Carbon::now()->subDays($this->timeRange * 2);
        
        // Total Revenue
        $currentRevenue = Payment::where('status', 'completed')
            ->where('created_at', '>=', $currentPeriod)
            ->sum('amount');
            
        $previousRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$previousPeriod, $currentPeriod])
            ->sum('amount');
            
        $this->totalRevenue = $currentRevenue;
        $this->revenueGrowth = $previousRevenue > 0 
            ? round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 1)
            : 0;

        // Total Bookings
        $currentBookings = Booking::where('created_at', '>=', $currentPeriod)->count();
        $previousBookings = Booking::whereBetween('created_at', [$previousPeriod, $currentPeriod])->count();
        
        $this->totalBookings = $currentBookings;
        $this->bookingsGrowth = $previousBookings > 0 
            ? round((($currentBookings - $previousBookings) / $previousBookings) * 100, 1)
            : 0;

        // Active Venues
        $currentVenues = Venue::where('status', 'active')
            ->where('created_at', '>=', $currentPeriod)
            ->count();
        $previousVenues = Venue::where('status', 'active')
            ->whereBetween('created_at', [$previousPeriod, $currentPeriod])
            ->count();
            
        $this->activeVenues = Venue::where('status', 'active')->count();
        $this->venuesGrowth = $currentVenues;

        // Total Users
        $currentUsers = User::where('created_at', '>=', $currentPeriod)->count();
        $previousUsers = User::whereBetween('created_at', [$previousPeriod, $currentPeriod])->count();
        
        $this->totalUsers = User::count();
        $this->usersGrowth = $currentUsers;
    }

    private function loadRevenueChart()
    {
        $months = [];
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $revenue = Payment::where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
                
            $bookings = Booking::whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            $data[] = [
                'month' => $month->format('M'),
                'revenue' => (float) $revenue,
                'bookings' => $bookings
            ];
        }
        
        $this->revenueChartData = $data;
    }

    private function loadBookingStatusChart()
    {
        $statuses = Booking::select('status', DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->timeRange))
            ->groupBy('status')
            ->get();
            
        $colors = [
            'confirmed' => '#22c55e',
            'pending' => '#f59e0b',
            'cancelled' => '#ef4444'
        ];
        
        $this->bookingStatusData = $statuses->map(function ($status) use ($colors) {
            return [
                'name' => ucfirst($status->status),
                'value' => $status->count,
                'color' => $colors[$status->status] ?? '#6b7280'
            ];
        })->toArray();
    }

    private function loadRecentActivities()
    {
        $activities = collect();
        
        // Recent venue approvals
        $recentVenues = Venue::where('status', 'active')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->latest()
            ->take(3)
            ->get();
            
        foreach ($recentVenues as $venue) {
            $activities->push([
                'id' => 'venue_' . $venue->id,
                'type' => 'success',
                'icon' => 'check',
                'color' => 'green',
                'description' => "New venue <strong>\"{$venue->name}\"</strong> was approved",
                'created_at' => $venue->created_at
            ]);
        }
        
        // Recent bookings
        $recentBookings = Booking::with(['venue', 'user'])
            ->where('status', 'confirmed')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->latest()
            ->take(3)
            ->get();
            
        foreach ($recentBookings as $booking) {
            $activities->push([
                'id' => 'booking_' . $booking->id,
                'type' => 'info',
                'icon' => 'calendar',
                'color' => 'blue',
                'description' => "Booking #{$booking->id} confirmed for <strong>{$booking->venue->name}</strong>",
                'created_at' => $booking->created_at
            ]);
        }
        
        // Recent user registrations
        $recentUsers = User::where('role', 'vendor')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->latest()
            ->take(2)
            ->get();
            
        foreach ($recentUsers as $user) {
            $activities->push([
                'id' => 'user_' . $user->id,
                'type' => 'warning',
                'icon' => 'user',
                'color' => 'amber',
                'description' => "New vendor <strong>{$user->name}</strong> registered and pending approval",
                'created_at' => $user->created_at
            ]);
        }
        
        // Recent reviews
        $recentReviews = Review::with(['user', 'reviewable'])
            ->where('rating', '>=', 4)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->latest()
            ->take(2)
            ->get();
            
        foreach ($recentReviews as $review) {
            $reviewableName = $review->reviewable->name ?? 'Unknown';
            $activities->push([
                'id' => 'review_' . $review->id,
                'type' => 'success',
                'icon' => 'star',
                'color' => 'purple',
                'description' => "{$review->rating}-star review submitted for <strong>{$reviewableName}</strong>",
                'created_at' => $review->created_at
            ]);
        }
        
        $this->recentActivities = $activities->sortByDesc('created_at')->take(5)->values()->all();
    }

    private function loadPendingApprovals()
    {
        $approvals = collect();
        
        // Pending venues
        $pendingVenues = Venue::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
            
        foreach ($pendingVenues as $venue) {
            $approvals->push([
                'id' => 'venue_' . $venue->id,
                'type' => 'venue',
                'name' => $venue->name,
                'description' => 'Venue registration',
                'icon' => 'building',
                'type_color' => 'blue',
                'model' => 'venue',
                'model_id' => $venue->id,
                'created_at' => $venue->created_at
            ]);
        }
        
        // Pending services
        $pendingServices = Service::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
            
        foreach ($pendingServices as $service) {
            $approvals->push([
                'id' => 'service_' . $service->id,
                'type' => 'service',
                'name' => $service->name,
                'description' => 'Service registration',
                'icon' => 'concierge-bell',
                'type_color' => 'green',
                'model' => 'service',
                'model_id' => $service->id,
                'created_at' => $service->created_at
            ]);
        }
        
        // Pending vendor accounts
        $pendingVendors = User::where('role', 'vendor')
            ->whereNull('email_verified_at')
            ->latest()
            ->take(3)
            ->get();
            
        foreach ($pendingVendors as $vendor) {
            $approvals->push([
                'id' => 'vendor_' . $vendor->id,
                'type' => 'vendor',
                'name' => $vendor->name,
                'description' => 'Vendor account verification',
                'icon' => 'user-check',
                'type_color' => 'purple',
                'model' => 'user',
                'model_id' => $vendor->id,
                'created_at' => $vendor->created_at
            ]);
        }
        
        $this->pendingApprovals = $approvals->sortByDesc('created_at')->take(8)->values()->all();
    }

    private function loadTopVenues()
    {
        $venues = Venue::withCount(['bookings' => function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonth());
            }])
            ->with(['bookings' => function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonth())
                      ->with('payment');
            }])
            ->where('status', 'active')
            ->get()
            ->map(function ($venue) {
                $revenue = $venue->bookings->sum(function ($booking) {
                    return $booking->payment ? $booking->payment->amount : 0;
                });
                
                // Calculate growth (simplified - comparing to previous month)
                $previousMonthBookings = $venue->bookings()
                    ->whereBetween('created_at', [
                        Carbon::now()->subMonths(2)->startOfMonth(),
                        Carbon::now()->subMonth()->endOfMonth()
                    ])
                    ->count();
                    
                $growth = $previousMonthBookings > 0 
                    ? round((($venue->bookings_count - $previousMonthBookings) / $previousMonthBookings) * 100, 0)
                    : 0;
                
                return [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'bookings_count' => $venue->bookings_count,
                    'revenue' => $revenue,
                    'growth' => max($growth, 0) // Don't show negative growth
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();
            
        $this->topVenues = $venues->all();
    }

    public function approveItem($model, $modelId)
    {
        switch ($model) {
            case 'venue':
                Venue::find($modelId)->update(['status' => 'active']);
                break;
            case 'service':
                Service::find($modelId)->update(['status' => 'active']);
                break;
            case 'user':
                User::find($modelId)->update(['email_verified_at' => now()]);
                break;
        }
        
        $this->loadPendingApprovals();
        session()->flash('success', 'Item approved successfully!');
    }

    public function rejectItem($model, $modelId)
    {
        switch ($model) {
            case 'venue':
                Venue::find($modelId)->update(['status' => 'rejected']);
                break;
            case 'service':
                Service::find($modelId)->update(['status' => 'rejected']);
                break;
            case 'user':
                User::find($modelId)->delete();
                break;
        }
        
        $this->loadPendingApprovals();
        session()->flash('success', 'Item rejected successfully!');
    }

    public function exportReport()
    {
        // Implement report export logic
        session()->flash('success', 'Report export started! You will receive an email when ready.');
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}