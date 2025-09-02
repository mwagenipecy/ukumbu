<?php

use App\Models\User;
use App\Models\Venue;
use App\Models\Service;
use App\Models\Category;
use App\Models\Booking;
use App\Models\BookingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

describe('Booking System End-to-End Tests', function () {
    
    beforeEach(function () {
        // Create test data
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->client = User::factory()->create(['role' => 'client']);
        $this->vendor = User::factory()->create(['role' => 'vendor']);
        
        $this->category = Category::create([
            'name' => 'Wedding Venue',
            'type' => 'venue'
        ]);
        
        $this->venue = Venue::create([
            'user_id' => $this->vendor->id,
            'category_id' => $this->category->id,
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
            'price' => 500.00,
            'status' => 'active'
        ]);
    });

    test('client can view available venues', function () {
        $response = $this->actingAs($this->client)
            ->get('/venue-management');
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    });

    test('client can view venue details', function () {
        $response = $this->actingAs($this->client)
            ->get("/view-venue-details/{$this->venue->id}");
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
        $response->assertSee($this->venue->description);
    });

    test('client can create a booking', function () {
        $bookingData = [
            'venue_id' => $this->venue->id,
            'event_date' => now()->addDays(30)->format('Y-m-d'),
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ];

        $response = $this->actingAs($this->client)
            ->post('/bookings', $bookingData);
        
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => $bookingData['event_date']
        ]);
    });

    test('prevents double booking for same venue and date', function () {
        // Create first booking
        $firstBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Try to create second booking for same date
        $secondBookingData = [
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ];

        $response = $this->actingAs($this->client)
            ->post('/bookings', $secondBookingData);
        
        // Should fail or redirect with error
        $this->assertDatabaseMissing('bookings', [
            'id' => 2,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25'
        ]);
    });

    test('admin can view all bookings', function () {
        // Create some test bookings
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bookings');
        
        $response->assertStatus(200);
        $response->assertSee($this->client->name);
        $response->assertSee($this->venue->name);
    });

    test('admin can approve booking', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/bookings/{$booking->id}/approve");
        
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed'
        ]);
    });

    test('admin can reject booking', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/bookings/{$booking->id}/reject");
        
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'rejected'
        ]);
    });

    test('vendor can view their venue bookings', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($this->vendor)
            ->get('/vendor/bookings');
        
        $response->assertStatus(200);
        $response->assertSee($this->client->name);
    });

    test('client can view their own bookings', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($this->client)
            ->get('/booking-management');
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    });

    test('client can cancel their own booking', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($this->client)
            ->patch("/bookings/{$booking->id}/cancel");
        
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled'
        ]);
    });

    test('booking calendar shows availability', function () {
        // Create confirmed booking
        $confirmedBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar');
        
        $response->assertStatus(200);
        $response->assertSee('2025-12-25');
        $response->assertSee($this->venue->name);
    });

    test('venue availability check prevents double booking', function () {
        $eventDate = '2025-12-25';
        
        // Create first booking
        $firstBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => $eventDate,
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Check if venue is available
        $isAvailable = !Booking::where('venue_id', $this->venue->id)
            ->where('event_date', $eventDate)
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();

        $this->assertFalse($isAvailable);
    });

    test('different dates allow multiple bookings', function () {
        $date1 = '2025-12-25';
        $date2 = '2025-12-26';
        
        // Create first booking
        $firstBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => $date1,
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Create second booking for different date
        $secondBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => $date2,
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $firstBooking->id,
            'event_date' => $date1
        ]);
        
        $this->assertDatabaseHas('bookings', [
            'id' => $secondBooking->id,
            'event_date' => $date2
        ]);
    });
});

