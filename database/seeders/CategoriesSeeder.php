<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        Category::insert([
            ['name' => 'Wedding Venues', 'type' => 'venue'],
            ['name' => 'Conference Halls', 'type' => 'venue'],
            ['name' => 'Catering', 'type' => 'service'],
            ['name' => 'Photography', 'type' => 'service'],
            ['name' => 'Music/DJ', 'type' => 'service'],
        ]);
    }
}

