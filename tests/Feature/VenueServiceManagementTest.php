<?php

use App\Models\User;
use App\Models\Venue;
use App\Models\Service;
use App\Models\Category;
use App\Models\Review;
use App\Models\Like;
use App\Models\Bookmark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

describe('Venue and Service Management End-to-End Tests', function () {
    
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->client = User::factory()->create(['role' => 'client']);
        $this->vendor = User::factory()->create(['role' => 'vendor']);
        
        $this->venueCategory = Category::create([
            'name' => 'Wedding Venue',
            'type' => 'venue'
        ]);
        
        $this->serviceCategory = Category::create([
            'name' => 'Wedding Service',
            'type' => 'service'
        ]);
        
        $this->venue = Venue::create([
            'user_id' => $this->vendor->id,
            'category_id' => $this->venueCategory->id,
            'name' => 'Test Venue',
            'description' => 'A beautiful test venue',
            'base_price' => 1000.00,
            'location_name' => 'Test Location',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'active'
        ]);
        
        $this->service = Service::create([
            'user_id' => $this->vendor->id,
            'name' => 'Wedding Service',
            'description' => 'Complete wedding service',
            'pricing_model' => 'flat',
            'price' => 500.00,
            'location_name' => 'Service Location',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'active'
        ]);
    });

    test('vendor can create a new venue', function () {
        $venueData = [
            'name' => 'New Venue',
            'description' => 'A new test venue',
            'base_price' => 1500.00,
            'location_name' => 'New Location',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'category_id' => $this->venueCategory->id,
            'status' => 'active'
        ];

        $response = $this->actingAs($this->vendor)
            ->post('/venues', $venueData);
        
        $this->assertDatabaseHas('venues', [
            'name' => 'New Venue',
            'user_id' => $this->vendor->id
        ]);
    });

    test('vendor can update their venue', function () {
        $updateData = [
            'name' => 'Updated Venue Name',
            'description' => 'Updated description',
            'base_price' => 1200.00
        ];

        $response = $this->actingAs($this->vendor)
            ->put("/venues/{$this->venue->id}", $updateData);
        
        $this->assertDatabaseHas('venues', [
            'id' => $this->venue->id,
            'name' => 'Updated Venue Name',
            'base_price' => 1200.00
        ]);
    });

    test('vendor cannot update other vendor venues', function () {
        $otherVendor = User::factory()->create(['role' => 'vendor']);
        $otherVenue = Venue::create([
            'user_id' => $otherVendor->id,
            'category_id' => $this->venueCategory->id,
            'name' => 'Other Venue',
            'description' => 'Another venue',
            'base_price' => 800.00,
            'location_name' => 'Other Location',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'active'
        ]);

        $updateData = ['name' => 'Hacked Venue'];

        $response = $this->actingAs($this->vendor)
            ->put("/venues/{$otherVenue->id}", $updateData);
        
        $response->assertStatus(403);
        
        $this->assertDatabaseMissing('venues', [
            'id' => $otherVenue->id,
            'name' => 'Hacked Venue'
        ]);
    });

    test('vendor can create a new service', function () {
        $serviceData = [
            'name' => 'New Service',
            'description' => 'A new test service',
            'pricing_model' => 'flat',
            'price' => 750.00,
            'location_name' => 'New Service Location',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'active'
        ];

        $response = $this->actingAs($this->vendor)
            ->post('/services', $serviceData);
        
        $this->assertDatabaseHas('services', [
            'name' => 'New Service',
            'user_id' => $this->vendor->id
        ]);
    });

    test('vendor can update their service', function () {
        $updateData = [
            'name' => 'Updated Service Name',
            'price' => 600.00
        ];

        $response = $this->actingAs($this->vendor)
            ->put("/services/{$this->service->id}", $updateData);
        
        $this->assertDatabaseHas('services', [
            'id' => $this->service->id,
            'name' => 'Updated Service Name',
            'price' => 600.00
        ]);
    });

    test('admin can approve venue', function () {
        $pendingVenue = Venue::create([
            'user_id' => $this->vendor->id,
            'category_id' => $this->venueCategory->id,
            'name' => 'Pending Venue',
            'description' => 'A pending venue',
            'base_price' => 1000.00,
            'location_name' => 'Pending Location',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/venues/{$pendingVenue->id}/approve");
        
        $this->assertDatabaseHas('venues', [
            'id' => $pendingVenue->id,
            'status' => 'active'
        ]);
    });

    test('admin can reject venue', function () {
        $pendingVenue = Venue::create([
            'user_id' => $this->vendor->id,
            'category_id' => $this->venueCategory->id,
            'name' => 'Pending Venue',
            'description' => 'A pending venue',
            'base_price' => 1000.00,
            'location_name' => 'Pending Location',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/venues/{$pendingVenue->id}/reject");
        
        $this->assertDatabaseHas('venues', [
            'id' => $pendingVenue->id,
            'status' => 'rejected'
        ]);
    });

    test('client can like a venue', function () {
        $response = $this->actingAs($this->client)
            ->post("/venues/{$this->venue->id}/like");
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->client->id,
            'likeable_id' => $this->venue->id,
            'likeable_type' => Venue::class
        ]);
    });

    test('client can unlike a venue', function () {
        // First like the venue
        Like::create([
            'user_id' => $this->client->id,
            'likeable_id' => $this->venue->id,
            'likeable_type' => Venue::class
        ]);

        $response = $this->actingAs($this->client)
            ->delete("/venues/{$this->venue->id}/like");
        
        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->client->id,
            'likeable_id' => $this->venue->id,
            'likeable_type' => Venue::class
        ]);
    });

    test('client can bookmark a venue', function () {
        $response = $this->actingAs($this->client)
            ->post("/venues/{$this->venue->id}/bookmark");
        
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->client->id,
            'bookmarkable_id' => $this->venue->id,
            'bookmarkable_type' => Venue::class
        ]);
    });

    test('client can remove bookmark', function () {
        // First bookmark the venue
        Bookmark::create([
            'user_id' => $this->client->id,
            'bookmarkable_id' => $this->venue->id,
            'bookmarkable_type' => Venue::class
        ]);

        $response = $this->actingAs($this->client)
            ->delete("/venues/{$this->venue->id}/bookmark");
        
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $this->client->id,
            'bookmarkable_id' => $this->venue->id,
            'bookmarkable_type' => Venue::class
        ]);
    });

    test('client can review a venue', function () {
        $reviewData = [
            'rating' => 5,
            'comment' => 'Excellent venue!'
        ];

        $response = $this->actingAs($this->client)
            ->post("/venues/{$this->venue->id}/reviews", $reviewData);
        
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'reviewable_type' => Venue::class,
            'rating' => 5,
            'comment' => 'Excellent venue!'
        ]);
    });

    test('client can review a service', function () {
        $reviewData = [
            'rating' => 4,
            'comment' => 'Great service!'
        ];

        $response = $this->actingAs($this->client)
            ->post("/services/{$this->service->id}/reviews", $reviewData);
        
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->client->id,
            'reviewable_id' => $this->service->id,
            'reviewable_type' => Service::class,
            'rating' => 4,
            'comment' => 'Great service!'
        ]);
    });

    test('admin can view all venues', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/venues');
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    });

    test('admin can view all services', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/services');
        
        $response->assertStatus(200);
        $response->assertSee($this->service->name);
    });

    test('admin can delete inappropriate reviews', function () {
        $review = Review::create([
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'reviewable_type' => Venue::class,
            'rating' => 1,
            'comment' => 'Inappropriate comment'
        ]);

        $response = $this->actingAs($this->admin)
            ->delete("/admin/reviews/{$review->id}");
        
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id
        ]);
    });

    test('venue search works correctly', function () {
        $response = $this->actingAs($this->client)
            ->get('/venue-management?search=Test');
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    });

    test('venue filtering by category works', function () {
        $response = $this->actingAs($this->client)
            ->get("/venue-management?category={$this->venueCategory->id}");
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    });

    test('venue filtering by price range works', function () {
        $response = $this->actingAs($this->client)
            ->get('/venue-management?min_price=500&max_price=1500');
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    });

    test('venue location-based search works', function () {
        $response = $this->actingAs($this->client)
            ->get('/venue-management?location=Test Location');
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    });

    test('vendor dashboard shows their venues and services', function () {
        $response = $this->actingAs($this->vendor)
            ->get('/vendor/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
        $response->assertSee($this->service->name);
    });

    test('vendor can view venue analytics', function () {
        $response = $this->actingAs($this->vendor)
            ->get("/vendor/venues/{$this->venue->id}/analytics");
        
        $response->assertStatus(200);
    });

    test('vendor can view service analytics', function () {
        $response = $this->actingAs($this->vendor)
            ->get("/vendor/services/{$this->service->id}/analytics");
        
        $response->assertStatus(200);
    });
});
