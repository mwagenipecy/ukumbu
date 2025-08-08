<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

  

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }




    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeLowRated($query)
    {
        return $query->where('rating', '<=', 2);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeHidden($query)
    {
        return $query->where('status', 'hidden');
    }

    // Status helper methods
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isHidden(): bool
    {
        return $this->status === 'hidden';
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public function hide(): void
    {
        $this->update(['status' => 'hidden']);
    }

    // Accessors
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y');
    }

    public function getStarRatingAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'approved' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'rejected' => 'bg-red-100 text-red-800',
            'hidden' => 'bg-gray-100 text-gray-800',
            default => 'bg-blue-100 text-blue-800',
        };
    }

    public function getRatingColorAttribute()
    {
        return match(true) {
            $this->rating >= 4 => 'text-green-600',
            $this->rating >= 3 => 'text-yellow-600',
            $this->rating >= 2 => 'text-orange-600',
            default => 'text-red-600',
        };
    }

    public function isFlagged(): bool
    {
        return $this->rating <= 2 || 
               str_contains(strtolower($this->comment ?? ''), 'spam') ||
               str_contains(strtolower($this->comment ?? ''), 'fake') ||
               strlen($this->comment ?? '') < 10; // Very short reviews might be spam
    }

    public function getReviewableTypeNameAttribute(): string
    {
        return match($this->reviewable_type) {
            Service::class => 'Service',
            Venue::class => 'Venue',
            default => 'Unknown',
        };
    }



}