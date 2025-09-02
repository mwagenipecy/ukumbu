<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    

    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'base_price',
        'price_min',
        'price_max',
        'price_type',
        'location_name',
        'latitude',
        'longitude',
        'status',
        'gallery',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
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

    public function services()
    {
        return $this->belongsToMany(Service::class, 'venue_service');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
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

    public function getFormattedPriceAttribute()
    {
        if ($this->price_min && $this->price_max) {
            if ($this->price_min == $this->price_max) {
                $price = 'TSh ' . number_format($this->price_min, 0);
            } else {
                $price = 'TSh ' . number_format($this->price_min, 0) . ' - ' . number_format($this->price_max, 0);
            }
            
            if ($this->price_type) {
                $price .= match($this->price_type) {
                    'per_hour' => '/hour',
                    'per_day' => '/day',
                    'per_event' => '/event',
                    default => ''
                };
            }
            
            return $price;
        }
        
        // Fallback to base_price if ranges not set
        return 'TSh ' . number_format($this->base_price ?? 0, 0);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

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
            return $query->whereBetween('base_price', [$min, $max]);
        } elseif ($min !== null) {
            return $query->where('base_price', '>=', $min);
        } elseif ($max !== null) {
            return $query->where('base_price', '<=', $max);
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







}
