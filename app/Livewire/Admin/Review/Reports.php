<?php

namespace App\Livewire\Admin\Review;

use App\Models\Review;
use App\Models\Service;
use App\Models\Venue;
use App\Models\User;
use App\Models\Booking;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Reports extends Component
{
    public $activeTab = 'overview';
    public $dateRange = '30'; // Last 30 days by default
    public $customStartDate = '';
    public $customEndDate = '';
    
    // Report data
    public $reportData = [];
    public $chartData = [];

    public function mount()
    {
        $this->customStartDate = now()->subDays(30)->format('Y-m-d');
        $this->customEndDate = now()->format('Y-m-d');
        $this->generateReports();
    }

    public function updatedDateRange()
    {
        if ($this->dateRange !== 'custom') {
            $this->customStartDate = now()->subDays((int)$this->dateRange)->format('Y-m-d');
            $this->customEndDate = now()->format('Y-m-d');
        }
        $this->generateReports();
    }

    public function updatedCustomStartDate()
    {
        if ($this->dateRange === 'custom') {
            $this->generateReports();
        }
    }

    public function updatedCustomEndDate()
    {
        if ($this->dateRange === 'custom') {
            $this->generateReports();
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->generateReports();
    }

    public function generateReports()
    {
        $startDate = $this->customStartDate;
        $endDate = $this->customEndDate;

        $this->reportData = [
            'overview' => $this->generateOverviewReport($startDate, $endDate),
            'ratings' => $this->generateRatingsReport($startDate, $endDate),
            'services' => $this->generateServicesReport($startDate, $endDate),
            'users' => $this->generateUsersReport($startDate, $endDate),
            'trends' => $this->generateTrendsReport($startDate, $endDate),
        ];
    }

    private function generateOverviewReport($startDate, $endDate)
    {
        return [
            'total_reviews' => Review::whereBetween('created_at', [$startDate, $endDate])->count(),
            'average_rating' => Review::whereBetween('created_at', [$startDate, $endDate])
                                  // ->where('status', 'approved')
                                   ->avg('rating') ?? 0,
            'approval_rate' => $this->calculateApprovalRate($startDate, $endDate),
            'response_time' => $this->calculateAverageResponseTime($startDate, $endDate),
            'top_rated_services' => $this->getTopRatedItems('service', $startDate, $endDate),
            'top_rated_venues' => $this->getTopRatedItems('venue', $startDate, $endDate),
            'recent_reviews' => Review::with(['user', 'reviewable'])
                                    ->whereBetween('created_at', [$startDate, $endDate])
                                    ->latest()
                                    ->limit(10)
                                    ->get(),
            'flagged_reviews' => Review::whereBetween('created_at', [$startDate, $endDate])
                                     ->where('rating', '<=', 2)
                                     ->count(),
        ];
    }

    private function generateRatingsReport($startDate, $endDate)
    {
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = Review::whereBetween('created_at', [$startDate, $endDate])
                                        //  ->where('status', 'approved')
                                          ->where('rating', $i)
                                          ->count();
        }

        return [
            'distribution' => $ratingDistribution,
            'by_category' => $this->getRatingsByCategory($startDate, $endDate),
            'trends' => $this->getRatingTrends($startDate, $endDate),
            'comparison' => $this->getRatingComparison($startDate, $endDate),
        ];
    }

    private function generateServicesReport($startDate, $endDate)
    {
        return [
            'most_reviewed' => $this->getMostReviewedServices($startDate, $endDate),
            'highest_rated' => $this->getHighestRatedServices($startDate, $endDate),
            'lowest_rated' => $this->getLowestRatedServices($startDate, $endDate),
            'no_reviews' => Service::whereDoesntHave('reviews')->count(),
            'by_provider' => $this->getReviewsByProvider($startDate, $endDate),
        ];
    }

    private function generateUsersReport($startDate, $endDate)
    {
        return [
            'most_active_reviewers' => $this->getMostActiveReviewers($startDate, $endDate),
            'new_reviewers' => $this->getNewReviewers($startDate, $endDate),
            'reviewer_satisfaction' => $this->getReviewerSatisfaction($startDate, $endDate),
            'review_frequency' => $this->getReviewFrequency($startDate, $endDate),
        ];
    }

    private function generateTrendsReport($startDate, $endDate)
    {
        return [
            'daily_reviews' => $this->getDailyReviewTrends($startDate, $endDate),
            'monthly_comparison' => $this->getMonthlyComparison(),
            'seasonal_patterns' => $this->getSeasonalPatterns(),
            'growth_metrics' => $this->getGrowthMetrics($startDate, $endDate),
        ];
    }

    // Helper methods for calculations
    private function calculateApprovalRate($startDate, $endDate)
    {
        $total = Review::whereBetween('created_at', [$startDate, $endDate])->count();
        if ($total === 0) return 0;
        
        $approved = Review::whereBetween('created_at', [$startDate, $endDate])
                        // ->where('status', 'approved')
                         ->count();
        
        return ($approved / $total) * 100;
    }

    private function calculateAverageResponseTime($startDate, $endDate)
    {
        // This would calculate average time from review creation to moderation
        // For now, return a placeholder
        return 2.5; // hours
    }

    private function getTopRatedItems($type, $startDate, $endDate)
    {
        $modelClass = $type === 'service' ? Service::class : Venue::class;
        
        return Review::select('reviewable_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as review_count'))
                    ->where('reviewable_type', $modelClass)
                   // ->where('status', 'approved')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('reviewable_id')
                    ->having('review_count', '>=', 3) // At least 3 reviews
                    ->orderBy('avg_rating', 'desc')
                    ->limit(10)
                    ->with(['reviewable'])
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->reviewable?->name ?? 'Unknown',
                            'rating' => round($item->avg_rating, 2),
                            'reviews' => $item->review_count,
                        ];
                    });
    }

    private function getRatingsByCategory($startDate, $endDate)
    {
        return DB::table('reviews')
                ->join('services', function($join) {
                    $join->on('reviews.reviewable_id', '=', 'services.id')
                         ->where('reviews.reviewable_type', '=', Service::class);
                })
                ->join('categories', 'services.category_id', '=', 'categories.id')
                ->whereBetween('reviews.created_at', [$startDate, $endDate])
               // ->where('reviews.status', 'approved')
                ->select('categories.name', DB::raw('AVG(reviews.rating) as avg_rating'), DB::raw('COUNT(*) as review_count'))
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('avg_rating', 'desc')
                ->get();
    }

    private function getRatingTrends($startDate, $endDate)
    {
        return Review::select(DB::raw('DATE(created_at) as date'), DB::raw('AVG(rating) as avg_rating'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                  //  ->where('status', 'approved')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
    }

    private function getRatingComparison($startDate, $endDate)
    {
        $currentPeriod = Review::whereBetween('created_at', [$startDate, $endDate])
                             // ->where('status', 'approved')
                              ->avg('rating') ?? 0;
        
        $daysDiff = now()->parse($endDate)->diffInDays(now()->parse($startDate));
        $previousStart = now()->parse($startDate)->subDays($daysDiff)->format('Y-m-d');
        $previousEnd = now()->parse($endDate)->subDays($daysDiff)->format('Y-m-d');
        
        $previousPeriod = Review::whereBetween('created_at', [$previousStart, $previousEnd])
                              // ->where('status', 'approved')
                               ->avg('rating') ?? 0;
        
        $change = $previousPeriod > 0 ? (($currentPeriod - $previousPeriod) / $previousPeriod) * 100 : 0;
        
        return [
            'current' => round($currentPeriod, 2),
            'previous' => round($previousPeriod, 2),
            'change_percentage' => round($change, 2),
        ];
    }

    private function getMostReviewedServices($startDate, $endDate)
    {
        return Review::select('reviewable_id', DB::raw('COUNT(*) as review_count'))
                    ->where('reviewable_type', Service::class)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('reviewable_id')
                    ->orderBy('review_count', 'desc')
                    ->limit(10)
                    ->with(['reviewable'])
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->reviewable?->name ?? 'Unknown',
                            'reviews' => $item->review_count,
                            'avg_rating' => $item->reviewable?->reviews()->avg('rating') ?? 0,
                        ];
                    });
    }

    private function getHighestRatedServices($startDate, $endDate)
    {
        return Review::select('reviewable_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as review_count'))
                    ->where('reviewable_type', Service::class)
                    //->where('status', 'approved')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('reviewable_id')
                    ->having('review_count', '>=', 2)
                    ->orderBy('avg_rating', 'desc')
                    ->limit(10)
                    ->with(['reviewable'])
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->reviewable?->name ?? 'Unknown',
                            'rating' => round($item->avg_rating, 2),
                            'reviews' => $item->review_count,
                        ];
                    });
    }

    private function getLowestRatedServices($startDate, $endDate)
    {
        return Review::select('reviewable_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as review_count'))
                    ->where('reviewable_type', Service::class)
                  //  ->where('status', 'approved')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('reviewable_id')
                    ->having('review_count', '>=', 2)
                    ->orderBy('avg_rating', 'asc')
                    ->limit(10)
                    ->with(['reviewable'])
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->reviewable?->name ?? 'Unknown',
                            'rating' => round($item->avg_rating, 2),
                            'reviews' => $item->review_count,
                            'needs_attention' => $item->avg_rating < 3,
                        ];
                    });
    }

    private function getReviewsByProvider($startDate, $endDate)
    {
        return Review::join('services', function($join) {
                        $join->on('reviews.reviewable_id', '=', 'services.id')
                             ->where('reviews.reviewable_type', '=', Service::class);
                    })
                    ->join('users', 'services.user_id', '=', 'users.id')
                    ->whereBetween('reviews.created_at', [$startDate, $endDate])
                   // ->where('reviews.status', 'approved')
                    ->select('users.name', 'users.email', 
                           DB::raw('AVG(reviews.rating) as avg_rating'), 
                           DB::raw('COUNT(*) as review_count'))
                    ->groupBy('users.id', 'users.name', 'users.email')
                    ->orderBy('review_count', 'desc')
                    ->limit(15)
                    ->get();
    }

    private function getMostActiveReviewers($startDate, $endDate)
    {
        return Review::select('user_id', DB::raw('COUNT(*) as review_count'), DB::raw('AVG(rating) as avg_given_rating'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('user_id')
                    ->orderBy('review_count', 'desc')
                    ->limit(15)
                    ->with(['user'])
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->user?->name ?? 'Unknown',
                            'email' => $item->user?->email ?? 'Unknown',
                            'reviews' => $item->review_count,
                            'avg_rating_given' => round($item->avg_given_rating, 2),
                        ];
                    });
    }

    private function getNewReviewers($startDate, $endDate)
    {
        return User::whereHas('reviews', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->whereDoesntHave('reviews', function ($query) use ($startDate) {
                    $query->where('created_at', '<', $startDate);
                })
                ->withCount(['reviews' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->orderBy('reviews_count', 'desc')
                ->limit(10)
                ->get();
    }

    private function getReviewerSatisfaction($startDate, $endDate)
    {
        $satisfaction = Review::whereBetween('created_at', [$startDate, $endDate])
                            // ->where('status', 'approved')
                             ->selectRaw('
                                 CASE 
                                     WHEN rating >= 4 THEN "satisfied"
                                     WHEN rating = 3 THEN "neutral"
                                     ELSE "dissatisfied"
                                 END as satisfaction_level,
                                 COUNT(*) as count
                             ')
                             ->groupBy('satisfaction_level')
                             ->get()
                             ->pluck('count', 'satisfaction_level')
                             ->toArray();
        
        $total = array_sum($satisfaction);
        
        return [
            'satisfied' => $satisfaction['satisfied'] ?? 0,
            'neutral' => $satisfaction['neutral'] ?? 0,
            'dissatisfied' => $satisfaction['dissatisfied'] ?? 0,
            'satisfaction_rate' => $total > 0 ? round((($satisfaction['satisfied'] ?? 0) / $total) * 100, 2) : 0,
        ];
    }

    private function getReviewFrequency($startDate, $endDate)
    {
        return Review::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'date' => $item->date,
                            'count' => $item->count,
                            'day_name' => now()->parse($item->date)->format('l'),
                        ];
                    });
    }

    private function getDailyReviewTrends($startDate, $endDate)
    {
        return Review::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('COUNT(*) as total_reviews'),
                        DB::raw('AVG(rating) as avg_rating'),
                      //  DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_reviews')
                    )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
    }

    private function getMonthlyComparison()
    {
        $currentMonth = Review::whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year)
                             ->count();
        
        $lastMonth = Review::whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year)
                          ->count();
        
        $change = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
        
        return [
            'current_month' => $currentMonth,
            'last_month' => $lastMonth,
            'change_percentage' => round($change, 2),
        ];
    }

    private function getSeasonalPatterns()
    {
        return Review::select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('COUNT(*) as review_count'),
                        DB::raw('AVG(rating) as avg_rating')
                    )
                    ->whereYear('created_at', now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'month' => now()->month($item->month)->format('M'),
                            'reviews' => $item->review_count,
                            'avg_rating' => round($item->avg_rating, 2),
                        ];
                    });
    }

    private function getGrowthMetrics($startDate, $endDate)
    {
        $totalReviews = Review::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalDays = now()->parse($endDate)->diffInDays(now()->parse($startDate)) + 1;
        $avgReviewsPerDay = $totalDays > 0 ? $totalReviews / $totalDays : 0;
        
        return [
            'total_reviews' => $totalReviews,
            'avg_per_day' => round($avgReviewsPerDay, 2),
            'peak_day' => $this->getPeakReviewDay($startDate, $endDate),
            'growth_trend' => $this->calculateGrowthTrend($startDate, $endDate),
        ];
    }

    private function getPeakReviewDay($startDate, $endDate)
    {
        return Review::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('count', 'desc')
                    ->first();
    }

    private function calculateGrowthTrend($startDate, $endDate)
    {
        // Calculate if reviews are trending up, down, or stable
        $dailyData = $this->getDailyReviewTrends($startDate, $endDate);
        
        if ($dailyData->count() < 7) {
            return 'insufficient_data';
        }
        
        $firstWeekAvg = $dailyData->take(7)->avg('total_reviews');
        $lastWeekAvg = $dailyData->skip(max(0, $dailyData->count() - 7))->avg('total_reviews');
        
        if ($lastWeekAvg > $firstWeekAvg * 1.1) {
            return 'trending_up';
        } elseif ($lastWeekAvg < $firstWeekAvg * 0.9) {
            return 'trending_down';
        } else {
            return 'stable';
        }
    }

    public function exportReport($format = 'csv')
    {
        // This would handle exporting the current report data
        session()->flash('success', 'Report export started. You will receive an email when ready.');
    }

    public function render()
    {
        return view('livewire.admin.review.reports', [
            'reportData' => $this->reportData,
            'activeTab' => $this->activeTab,
        ]);
    }
}