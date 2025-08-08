<?php

namespace App\Livewire\Admin\Review;

use App\Models\Review;
use App\Models\Service;
use App\Models\Venue;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class Index extends Component
{
    use WithPagination;

    // Search and filter properties
    public string $search = '';
    public string $ratingFilter = '';
    public string $typeFilter = '';
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 15;
    
    // Bulk actions
    public array $selectedReviews = [];
    public bool $selectAll = false;
    public bool $showBulkModal = false;
    
    // Review moderation
    public bool $showModerationModal = false;
    public ?Review $moderatingReview = null;
    public string $moderationAction = '';
    public string $moderationReason = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'ratingFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRatingFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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
        $this->reset(['search', 'ratingFilter', 'typeFilter', 'statusFilter']);
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedReviews = $this->getFilteredReviews()->pluck('id')->toArray();
        } else {
            $this->selectedReviews = [];
        }
    }

    public function openModerationModal($reviewId, $action)
    {
        $this->moderatingReview = Review::with(['user', 'reviewable'])->findOrFail($reviewId);
        $this->moderationAction = $action;
        $this->moderationReason = '';
        $this->showModerationModal = true;
    }

    public function closeModerationModal()
    {
        $this->showModerationModal = false;
        $this->moderatingReview = null;
        $this->moderationAction = '';
        $this->moderationReason = '';
    }

    public function moderateReview()
    {
        $this->validate([
            'moderationReason' => 'required|string|max:500',
        ]);

        switch ($this->moderationAction) {
            case 'approve':
                $this->moderatingReview->update(['status' => 'approved']);
                $message = 'Review approved successfully.';
                break;
            case 'reject':
                $this->moderatingReview->update(['status' => 'rejected']);
                $message = 'Review rejected successfully.';
                break;
            case 'hide':
                $this->moderatingReview->update(['status' => 'hidden']);
                $message = 'Review hidden successfully.';
                break;
            case 'delete':
                $this->moderatingReview->delete();
                $message = 'Review deleted successfully.';
                break;
            default:
                $message = 'Unknown action.';
        }

        // Here you could log the moderation action
        // ModerationLog::create([
        //     'review_id' => $this->moderatingReview->id,
        //     'action' => $this->moderationAction,
        //     'reason' => $this->moderationReason,
        //     'moderated_by' => auth()->id(),
        // ]);

        session()->flash('success', $message);
        $this->closeModerationModal();
    }

    public function toggleBulkModal()
    {
        $this->showBulkModal = !$this->showBulkModal;
    }

    public function bulkApprove()
    {
        Review::whereIn('id', $this->selectedReviews)->update(['status' => 'approved']);
        
        $this->selectedReviews = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected reviews approved successfully.');
    }

    public function bulkReject()
    {
        Review::whereIn('id', $this->selectedReviews)->update(['status' => 'rejected']);
        
        $this->selectedReviews = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected reviews rejected successfully.');
    }

    public function bulkHide()
    {
        Review::whereIn('id', $this->selectedReviews)->update(['status' => 'hidden']);
        
        $this->selectedReviews = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected reviews hidden successfully.');
    }

    public function bulkDelete()
    {
        Review::whereIn('id', $this->selectedReviews)->delete();
        
        $this->selectedReviews = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected reviews deleted successfully.');
    }

    public function quickApprove($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->update(['status' => 'approved']);
        
        session()->flash('success', 'Review approved successfully.');
    }

    public function quickReject($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->update(['status' => 'rejected']);
        
        session()->flash('success', 'Review rejected successfully.');
    }

    public function quickHide($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->update(['status' => 'hidden']);
        
        session()->flash('success', 'Review hidden successfully.');
    }

    private function getFilteredReviews()
    {
        return Review::query()
            ->with(['user', 'reviewable'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('comment', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function (Builder $userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHasMorph('reviewable', [Service::class, Venue::class], function (Builder $reviewableQuery) {
                          $reviewableQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->ratingFilter, function (Builder $query) {
                $query->where('rating', $this->ratingFilter);
            })
            ->when($this->typeFilter, function (Builder $query) {
                if ($this->typeFilter === 'service') {
                    $query->where('reviewable_type', Service::class);
                } elseif ($this->typeFilter === 'venue') {
                    $query->where('reviewable_type', Venue::class);
                }
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $reviews = $this->getFilteredReviews()->paginate($this->perPage);
        
        $stats = [
            'total' => Review::count(),
            'pending' => Review::count(),
            'approved' => Review::count(),
            'rejected' => Review::count(),
           'hidden' => Review::count(),
            'average_rating' => Review::avg('rating') ?? 0,
            'this_month' => Review::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count(),
            'flagged' => Review::where('rating', '<=', 2)->count(), // Low rating reviews
        ];

        // Rating distribution
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[$i] = Review::
                                          where('rating', $i)
                                          ->count();
        }

        return view('livewire.admin.review.index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'ratingDistribution' => $ratingDistribution,
        ]);
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'approved' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'rejected' => 'bg-red-100 text-red-800',
            'hidden' => 'bg-gray-100 text-gray-800',
            default => 'bg-blue-100 text-blue-800',
        };
    }

    public function getRatingColor($rating)
    {
        return match(true) {
            $rating >= 4.5 => 'text-green-600',
            $rating >= 3.5 => 'text-yellow-600',
            $rating >= 2.5 => 'text-orange-600',
            default => 'text-red-600',
        };
    }

    public function getRatingStars($rating)
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<i class="fas fa-star text-yellow-400"></i>';
            } else {
                $stars .= '<i class="fas fa-star text-gray-300"></i>';
            }
        }
        return $stars;
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