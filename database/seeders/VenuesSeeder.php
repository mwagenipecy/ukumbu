<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Venue;
use App\Models\User;
use App\Models\Category;

class VenuesSeeder extends Seeder
{
    public function run()
    {
        $vendor = User::where('email', 'venue@example.com')->first();
        $category = Category::where('type', 'venue')->first();

        Venue::create([
            'user_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Palm Garden Venue',
            'description' => 'Beautiful outdoor venue suitable for weddings and private events.',
            'base_price' => 500000,
            'location_name' => 'Dar es Salaam, Tanzania',
            'latitude' => -6.7924,
            'longitude' => 39.2083,
            'gallery' => json_encode([
                'https://example.com/img1.jpg',
                'https://example.com/img2.jpg'
            ]),
        ]);
    }
}
