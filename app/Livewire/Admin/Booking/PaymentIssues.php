<?php

namespace App\Livewire\Admin\Booking;

use App\Models\Booking;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class PaymentIssues extends Component
{
    use WithPagination;

    // Search and filter properties
    public string $search = '';
    public string $issueTypeFilter = '';
    public string $sortField = 'updated_at';
    public string $sortDirection = 'desc';
    public int $perPage = 20;
    
    // Bulk actions
    public array $selectedBookings = [];
    public bool $selectAll = false;
    
    // Payment resolution
    public bool $showResolutionModal = false;
    public ?Booking $resolvingBooking = null;
    public string $resolutionAction = '';
    public string $resolutionNote = '';
    public string $refundAmount = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'issueTypeFilter' => ['except' => ''],
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

    public function updatingIssueTypeFilter()
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
            $this->sortDirection = 'desc';
        }
        
        $this->sortField = $field;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'issueTypeFilter']);
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

    public function openResolutionModal($bookingId, $action)
    {
        $this->resolvingBooking = Booking::with(['user', 'venue', 'bookingItems.service', 'payments'])
                                        ->findOrFail($bookingId);
        $this->resolutionAction = $action;
        $this->resolutionNote = '';
        $this->refundAmount = '';
        $this->showResolutionModal = true;
    }

    public function closeResolutionModal()
    {
        $this->showResolutionModal = false;
        $this->resolvingBooking = null;
        $this->resolutionAction = '';
        $this->resolutionNote = '';
        $this->refundAmount = '';
    }

    public function resolvePaymentIssue()
    {
        $rules = [
            'resolutionNote' => 'required|string|max:500',
        ];

        if ($this->resolutionAction === 'refund') {
            $rules['refundAmount'] = 'required|numeric|min:0|max:' . $this->resolvingBooking->total_amount;
        }

        $this->validate($rules);

        switch ($this->resolutionAction) {
            case 'mark_paid':
                $this->resolvingBooking->update(['payment_status' => 'paid']);
                $message = 'Payment marked as completed successfully.';
                break;
            case 'retry_payment':
                // Here you would integrate with payment gateway to retry
                $this->resolvingBooking->update(['payment_status' => 'unpaid']);
                $message = 'Payment retry initiated successfully.';
                break;
            case 'refund':
                // Process refund through payment gateway
                $this->resolvingBooking->update([
                    'payment_status' => 'refunded',
                    'status' => 'cancelled'
                ]);
                $message = 'Refund processed successfully.';
                break;
            case 'dispute_resolved':
                $this->resolvingBooking->update(['payment_status' => 'paid']);
                $message = 'Payment dispute resolved successfully.';
                break;
            default:
                $message = 'Payment issue resolved.';
        }

        // Log the resolution
        // PaymentResolutionLog::create([
        //     'booking_id' => $this->resolvingBooking->id,
        //     'action' => $this->resolutionAction,
        //     'note' => $this->resolutionNote,
        //     'refund_amount' => $this->refundAmount ?: null,
        //     'resolved_by' => auth()->id(),
        // ]);

        session()->flash('success', $message);
        $this->closeResolutionModal();
    }

    public function quickMarkPaid($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->update(['payment_status' => 'paid']);
        
        session()->flash('success', 'Payment marked as completed for booking #' . $booking->id);
    }

    public function quickRetryPayment($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->update(['payment_status' => 'unpaid']);
        
        session()->flash('success', 'Payment retry initiated for booking #' . $booking->id);
    }

    public function bulkMarkPaid()
    {
        Booking::whereIn('id', $this->selectedBookings)->update(['payment_status' => 'paid']);
        
        $count = count($this->selectedBookings);
        $this->selectedBookings = [];
        $this->selectAll = false;
        
        session()->flash('success', $count . ' payments marked as completed successfully.');
    }

    public function bulkRetryPayment()
    {
        Booking::whereIn('id', $this->selectedBookings)->update(['payment_status' => 'unpaid']);
        
        $count = count($this->selectedBookings);
        $this->selectedBookings = [];
        $this->selectAll = false;
        
        session()->flash('success', $count . ' payment retries initiated successfully.');
    }

    private function getFilteredBookings()
    {
        return Booking::query()
            ->with(['user', 'venue', 'bookingItems.service', 'payments'])
            ->where(function (Builder $query) {
                $query->where('payment_status', 'failed')
                      ->orWhere('payment_status', 'disputed')
                      ->orWhere('payment_status', 'unpaid');
            })
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
            ->when($this->issueTypeFilter, function (Builder $query) {
                $query->where('payment_status', $this->issueTypeFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $bookings = $this->getFilteredBookings()->paginate($this->perPage);
        
        $stats = [
            'total_issues' => Booking::whereIn('payment_status', ['failed', 'disputed', 'unpaid'])->count(),
            'failed_payments' => Booking::where('payment_status', 'failed')->count(),
            'disputed_payments' => Booking::where('payment_status', 'disputed')->count(),
            'unpaid_bookings' => Booking::where('payment_status', 'unpaid')->count(),
            'total_pending_amount' => Booking::whereIn('payment_status', ['failed', 'disputed', 'unpaid'])->sum('total_amount'),
            'urgent_issues' => Booking::whereIn('payment_status', ['failed', 'disputed', 'unpaid'])
                                     ->where('event_date', '<=', now()->addDays(3))
                                     ->count(),
        ];

        return view('livewire.admin.booking.payment-issues', [
            'bookings' => $bookings,
            'stats' => $stats,
        ]);
    }

    public function getIssueTypeColor($paymentStatus)
    {
        return match($paymentStatus) {
            'failed' => 'bg-red-100 text-red-800 border-red-200',
            'disputed' => 'bg-purple-100 text-purple-800 border-purple-200',
            'unpaid' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getIssueTypeIcon($paymentStatus)
    {
        return match($paymentStatus) {
            'failed' => 'fas fa-times-circle text-red-500',
            'disputed' => 'fas fa-exclamation-triangle text-purple-500',
            'unpaid' => 'fas fa-clock text-yellow-500',
            default => 'fas fa-question-circle text-gray-500',
        };
    }

    public function getIssueDescription($paymentStatus)
    {
        return match($paymentStatus) {
            'failed' => 'Payment processing failed',
            'disputed' => 'Payment under dispute',
            'unpaid' => 'Payment not completed',
            default => 'Unknown payment issue',
        };
    }

    public function getIssueSeverity($booking)
    {
        $daysUntilEvent = now()->diffInDays($booking->event_date, false);
        
        if ($daysUntilEvent <= 0) {
            return 'critical'; // Event is today or past
        } elseif ($daysUntilEvent <= 1) {
            return 'urgent';   // Event tomorrow
        } elseif ($daysUntilEvent <= 3) {
            return 'moderate'; // Event within 3 days
        } else {
            return 'normal';
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