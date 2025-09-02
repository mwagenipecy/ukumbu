<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'venue_id',
        'event_date',
        'event_time',
        'guest_count',
        'special_requests',
        'status',
        'total_amount',
        'payment_status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'total_amount' => 'decimal:2',
    ];


    public function getDaysUntilEvent()
    {
        if (!$this->event_date) {
            return null; // or throw an exception if needed
        }

        $eventDate = Carbon::parse($this->event_date);
        $today = Carbon::today();

        return $today->diffInDays($eventDate, false); 
        // false means it can return negative if event is in the past
    }

 
    public function hasPaymentIssues()
    {
        return $this->payment_status !== 'paid';
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }


    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function getFormattedTotalAmountAttribute()
    {
        return 'TSh ' . number_format($this->total_amount, 0);
    }

    public function getBookingReferenceAttribute()
    {
        return 'BK' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }





    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }


    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now()->toDateString());
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return 'TSh ' . number_format($this->total_amount, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'confirmed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return !$this->isCancelled() && $this->event_date->isFuture();
    }



}