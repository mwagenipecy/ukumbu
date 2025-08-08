<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\User;
use App\Models\Category;

class ServicesSeeder extends Seeder
{
    public function run()
    {
        $provider = User::where('email', 'service@example.com')->first();
        $photoCategory = Category::where('name', 'Photography')->first();
        $musicCategory = Category::where('name', 'Music/DJ')->first();

        Service::create([
            'user_id' => $provider->id,
            'category_id' => $photoCategory->id,
            'name' => 'Elite Photography',
            'description' => 'Professional wedding and event photography.',
            'pricing_model' => 'flat',
            'price' => 250000,
            'location_name' => 'Arusha, Tanzania',
            'latitude' => -3.3869,
            'longitude' => 36.6820,
            'gallery' => json_encode([
                'https://example.com/photo1.jpg',
                'https://example.com/photo2.jpg'
            ]),
        ]);

        Service::create([
            'user_id' => $provider->id,
            'category_id' => $musicCategory->id,
            'name' => 'DJ Maestro',
            'description' => 'Top-rated DJ for weddings and corporate events.',
            'pricing_model' => 'hourly',
            'price' => 50000,
            'location_name' => 'Mwanza, Tanzania',
            'latitude' => -2.5164,
            'longitude' => 32.9172,
            'gallery' => json_encode([
                'https://example.com/dj1.jpg',
                'https://example.com/dj2.jpg'
            ]),
        ]);
    }
}
