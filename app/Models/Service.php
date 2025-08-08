<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'pricing_model',
        'price',
        'location_name',
        'latitude',
        'longitude',
        'status',
        'gallery',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'gallery' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'venue_service');
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Accessors
    public function getFirstImageAttribute()
    {
        if (is_array($this->gallery) && count($this->gallery) > 0) {
            return asset('storage/' . $this->gallery[0]);
        }
        return null;
    }

    public function hasImages(): bool
    {
        return !empty($this->gallery) && count($this->gallery) > 0;
    }


    public function getFirstImage(): ?string
    {
        if (!$this->gallery || empty($this->gallery)) {
            return null;
        }

        return $this->gallery[0] ?? null;
    }

    public function getFormattedPriceAttribute()
    {
        $price = 'TSh ' . number_format($this->price, 0);
        
        if ($this->pricing_model === 'hourly') {
            $price .= '/hr';
        } elseif ($this->pricing_model === 'per_guest') {
            $price .= '/guest';
        }
        
        return $price;
    }

    // public function getAverageRatingAttribute()
    // {
    //     return $this->reviews()->avg('rating') ?? 0;
    // }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getBookmarksCountAttribute()
    {
        return $this->bookmarks()->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeNearby($query, $latitude, $longitude, $radius = 50)
    {
        return $query->selectRaw("*, (
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * sin(radians(latitude))
            )
        ) AS distance", [$latitude, $longitude, $latitude])
        ->having('distance', '<=', $radius)
        ->orderBy('distance');
    }

    public function scopeInPriceRange($query, $min = null, $max = null)
    {
        if ($min !== null && $max !== null) {
            return $query->whereBetween('price', [$min, $max]);
        } elseif ($min !== null) {
            return $query->where('price', '>=', $min);
        } elseif ($max !== null) {
            return $query->where('price', '<=', $max);
        }
        
        return $query;
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('location_name', 'like', "%{$term}%");
        });
    }

    public function scopeByPricingModel($query, $model)
    {
        return $query->where('pricing_model', $model);
    }





public function services()
{
    return $this->hasMany(Service::class);
}

// public function venues()
// {
//     return $this->hasMany(Venue::class);
// }

public function bookings()
{
    return $this->hasMany(Booking::class);
}





public function payments()
{
    return $this->hasManyThrough(Payment::class, Booking::class);
}

// Scopes
public function scopeVendors($query)
{
    return $query->where('role', 'vendor');
}

public function scopeClients($query)
{
    return $query->where('role', 'client');
}

public function scopeAdmins($query)
{
    return $query->where('role', 'admin');
}

// Helper methods
public function isVendor(): bool
{
    return $this->role === 'vendor';
}

public function isClient(): bool
{
    return $this->role === 'client';
}

public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function getActiveServicesCount(): int
{
    return $this->services()->where('status', 'active')->count();
}

public function getTotalRevenueAttribute(): float
{
    return $this->services()
                ->join('booking_items', 'services.id', '=', 'booking_items.service_id')
                ->sum('booking_items.price');
}

public function getAverageRatingAttribute(): float
{
    $serviceIds = $this->services()->pluck('id');
    return Review::whereIn('reviewable_id', $serviceIds)
                ->where('reviewable_type', Service::class)
               
                ->avg('rating') ?? 0;
}



}
