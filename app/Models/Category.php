<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function scopeForVenues($query)
    {
        return $query->where('type', 'venue');
    }

    public function scopeForServices($query)
    {
        return $query->where('type', 'service');
    }
}