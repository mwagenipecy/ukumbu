<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'current_team_id',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // Relationships
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function payments(): HasManyThrough
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

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
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

    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function getActiveServicesCount(): int
    {
        return $this->services()->where('status', 'active')->count();
    }

    public function getPendingServicesCount(): int
    {
        return $this->services()->where('status', 'pending')->count();
    }

    public function getTotalRevenueAttribute(): float
    {
        return $this->services()
                    ->join('booking_items', 'services.id', '=', 'booking_items.service_id')
                    ->sum('booking_items.price') ?? 0;
    }

    public function getAverageRatingAttribute(): float
    {
        $serviceIds = $this->services()->pluck('id');
        
        if ($serviceIds->isEmpty()) {
            return 0;
        }

        return Review::whereIn('reviewable_id', $serviceIds)
                    ->where('reviewable_type', Service::class)
                    ->avg('rating') ?? 0;
    }

    public function getTotalBookingsAttribute(): int
    {
        if ($this->isClient()) {
            return $this->bookings()->count();
        } elseif ($this->isVendor()) {
            return $this->services()
                        ->join('booking_items', 'services.id', '=', 'booking_items.service_id')
                        ->count();
        }
        
        return 0;
    }

    public function getConfirmedBookingsAttribute(): int
    {
        if ($this->isClient()) {
            return $this->bookings()->where('status', 'confirmed')->count();
        } elseif ($this->isVendor()) {
            return $this->services()
                        ->join('booking_items', 'services.id', '=', 'booking_items.service_id')
                        ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                        ->where('bookings.status', 'confirmed')
                        ->count();
        }
        
        return 0;
    }

    public function getUpcomingBookingsAttribute(): int
    {
        if ($this->isClient()) {
            return $this->bookings()->where('event_date', '>=', now())->count();
        } elseif ($this->isVendor()) {
            return $this->services()
                        ->join('booking_items', 'services.id', '=', 'booking_items.service_id')
                        ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                        ->where('bookings.event_date', '>=', now())
                        ->count();
        }
        
        return 0;
    }

    public function getTotalSpentAttribute(): float
    {
        if ($this->isClient()) {
            return $this->bookings()->sum('total_amount') ?? 0;
        }
        
        return 0;
    }

    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            'admin' => 'bg-red-100 text-red-800',
            'vendor' => 'bg-blue-100 text-blue-800',
            'client' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getRoleIconAttribute(): string
    {
        return match($this->role) {
            'admin' => 'fas fa-user-shield',
            'vendor' => 'fas fa-store',
            'client' => 'fas fa-user',
            default => 'fas fa-question-circle',
        };
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    public function getAvatarColorAttribute(): string
    {
        $colors = [
            'bg-blue-100 text-blue-600',
            'bg-purple-100 text-purple-600',
            'bg-green-100 text-green-600',
            'bg-yellow-100 text-yellow-600',
            'bg-pink-100 text-pink-600',
            'bg-indigo-100 text-indigo-600',
        ];
        
        return $colors[$this->id % count($colors)];
    }

    // Methods for impersonation
    public function canBeImpersonated(): bool
    {
        return $this->id !== auth()->id();
    }

    public function canImpersonate(): bool
    {
        return $this->isAdmin();
    }
}