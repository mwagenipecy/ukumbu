<?php

use App\Models\User;
use App\Models\Venue;
use App\Models\Service;
use App\Models\Category;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

uses(RefreshDatabase::class, WithFaker::class);

describe('Admin Calendar System End-to-End Tests', function () {
    
    beforeEach(function () {
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
            'pricing_model' => 'flat',
            'price' => 500.00,
            'location_name' => 'Service Location',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'active'
        ]);
    });

    test('admin can view calendar with all bookings', function () {
        // Create bookings for different dates
        $booking1 = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $booking2 = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-26',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar');
        
        $response->assertStatus(200);
        $response->assertSee('2025-12-25');
        $response->assertSee('2025-12-26');
        $response->assertSee($this->venue->name);
        $response->assertSee($this->client->name);
    });

    test('calendar shows venue availability correctly', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?venue_id=' . $this->venue->id);
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    });

    test('calendar prevents double booking for same venue and date', function () {
        // Create first confirmed booking
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

        $response = $this->actingAs($this->admin)
            ->post('/admin/bookings', $secondBookingData);
        
        // Should fail validation
        $response->assertSessionHasErrors(['event_date']);
        
        $this->assertDatabaseMissing('bookings', [
            'id' => 2,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25'
        ]);
    });

    test('calendar allows booking for different dates', function () {
        // Create first booking
        $firstBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Create second booking for different date
        $secondBookingData = [
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-26',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/bookings', $secondBookingData);
        
        // Should succeed
        $this->assertDatabaseHas('bookings', [
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-26'
        ]);
    });

    test('calendar shows booking conflicts clearly', function () {
        // Create conflicting bookings
        $confirmedBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $pendingBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?date=2025-12-25');
        
        $response->assertStatus(200);
        $response->assertSee('CONFLICT');
        $response->assertSee('Double Booking Detected');
    });

    test('admin can resolve booking conflicts', function () {
        // Create conflicting bookings
        $confirmedBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $conflictingBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        // Admin resolves conflict by rejecting the conflicting booking
        $response = $this->actingAs($this->admin)
            ->patch("/admin/bookings/{$conflictingBooking->id}/reject", [
                'reason' => 'Double booking conflict - venue already booked'
            ]);
        
        $this->assertDatabaseHas('bookings', [
            'id' => $conflictingBooking->id,
            'status' => 'rejected'
        ]);
    });

    test('calendar shows venue availability status', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar/availability');
        
        $response->assertStatus(200);
        $response->assertSee('Available');
        $response->assertSee('Booked');
        $response->assertSee('Pending');
    });

    test('calendar can filter by date range', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?start_date=2025-12-01&end_date=2025-12-31');
        
        $response->assertStatus(200);
        $response->assertSee('December 2025');
    });

    test('calendar can filter by venue', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?venue_id=' . $this->venue->id);
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    });

    test('calendar can filter by booking status', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?status=confirmed');
        
        $response->assertStatus(200);
    });

    test('calendar shows payment status for each booking', function () {
        $booking = Booking::create([
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
        $response->assertSee('Paid');
    });

    test('calendar can export data', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar/export?format=csv');
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    });

    test('calendar shows venue capacity information', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?show_capacity=true');
        
        $response->assertStatus(200);
        $response->assertSee('Capacity');
    });

    test('calendar prevents overbooking beyond venue capacity', function () {
        // Set venue capacity
        $this->venue->update(['capacity' => 100]);

        // Create multiple bookings for same date
        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create(['role' => 'client']);
            Booking::create([
                'user_id' => $user->id,
                'venue_id' => $this->venue->id,
                'event_date' => '2025-12-25',
                'total_amount' => 1000.00,
                'status' => 'confirmed',
                'payment_status' => 'paid'
            ]);
        }

        // Try to book one more
        $overbookingData = [
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/bookings', $overbookingData);
        
        // Should fail due to capacity limit
        $response->assertSessionHasErrors(['venue_id']);
    });

    test('calendar shows recurring booking patterns', function () {
        // Create recurring bookings
        for ($i = 1; $i <= 4; $i++) {
            $date = Carbon::parse('2025-12-25')->addWeeks($i);
            Booking::create([
                'user_id' => $this->client->id,
                'venue_id' => $this->venue->id,
                'event_date' => $date->format('Y-m-d'),
                'total_amount' => 1000.00,
                'status' => 'confirmed',
                'payment_status' => 'paid'
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?show_patterns=true');
        
        $response->assertStatus(200);
        $response->assertSee('Recurring Pattern');
    });

    test('calendar can handle time slots for same date', function () {
        // Create morning booking
        $morningBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'start_time' => '09:00:00',
            'end_time' => '13:00:00',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Create afternoon booking for same date
        $afternoonBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'start_time' => '14:00:00',
            'end_time' => '18:00:00',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?date=2025-12-25');
        
        $response->assertStatus(200);
        $response->assertSee('09:00 - 13:00');
        $response->assertSee('14:00 - 18:00');
    });

    test('calendar prevents overlapping time slots', function () {
        // Create first booking
        $firstBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'start_time' => '10:00:00',
            'end_time' => '14:00:00',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Try to create overlapping booking
        $overlappingData = [
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'start_time' => '12:00:00',
            'end_time' => '16:00:00',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/bookings', $overlappingData);
        
        // Should fail due to time overlap
        $response->assertSessionHasErrors(['start_time', 'end_time']);
    });

    test('calendar shows venue maintenance periods', function () {
        // Set venue maintenance period
        $this->venue->update([
            'maintenance_start' => '2025-12-20',
            'maintenance_end' => '2025-12-22'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?show_maintenance=true');
        
        $response->assertStatus(200);
        $response->assertSee('Maintenance');
        $response->assertSee('2025-12-20');
        $response->assertSee('2025-12-22');
    });

    test('calendar prevents booking during maintenance', function () {
        // Set venue maintenance period
        $this->venue->update([
            'maintenance_start' => '2025-12-20',
            'maintenance_end' => '2025-12-22'
        ]);

        // Try to book during maintenance
        $maintenanceBookingData = [
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-21',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/bookings', $maintenanceBookingData);
        
        // Should fail due to maintenance
        $response->assertSessionHasErrors(['event_date']);
    });

    test('calendar can handle multiple venues', function () {
        $venue2 = Venue::create([
            'user_id' => $this->vendor->id,
            'category_id' => $this->category->id,
            'name' => 'Test Venue 2',
            'description' => 'Another test venue',
            'base_price' => 800.00,
            'location_name' => 'Test Location 2',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar?show_all_venues=true');
        
        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
        $response->assertSee($venue2->name);
    });

    test('calendar shows booking statistics', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar/statistics');
        
        $response->assertStatus(200);
        $response->assertSee('Total Bookings');
        $response->assertSee('Revenue');
        $response->assertSee('Occupancy Rate');
    });
});
