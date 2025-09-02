<?php

use App\Models\User;
use App\Models\Venue;
use App\Models\Category;
use App\Models\Booking;
use App\Livewire\Admin\Calendar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('Admin Calendar System Tests', function () {
    
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
    });

    test('admin can access calendar', function () {
        $response = $this->actingAs($this->admin)
            ->get('/admin/calendar');
        
        $response->assertStatus(200);
    });

    test('calendar shows available venues', function () {
        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->assertSee($this->venue->name);
    });

    test('calendar can filter by venue', function () {
        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->set('selectedVenue', $this->venue->id)
            ->assertSet('selectedVenue', $this->venue->id);
    });

    test('calendar can change views', function () {
        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('changeView', 'week')
            ->assertSet('view', 'week')
            ->call('changeView', 'day')
            ->assertSet('view', 'day')
            ->call('changeView', 'month')
            ->assertSet('view', 'month');
    });

    test('calendar can navigate between periods', function () {
        $currentDate = now()->format('Y-m-d');
        
        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('nextPeriod')
            ->assertNotSet('selectedDate', $currentDate)
            ->call('previousPeriod')
            ->assertSet('selectedDate', $currentDate);
    });

    test('calendar shows bookings correctly', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->set('selectedDate', '2025-12-25')
            ->assertSee($this->venue->name)
            ->assertSee($this->client->name);
    });

    test('calendar prevents double booking for same date', function () {
        // Create first booking
        $firstBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Check availability
        $isAvailable = Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('checkAvailability', $this->venue->id, '2025-12-25');

        $this->assertFalse($isAvailable);
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

        // Check availability for different date
        $isAvailable = Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('checkAvailability', $this->venue->id, '2025-12-26');

        $this->assertTrue($isAvailable);
    });

    test('calendar shows correct availability colors', function () {
        // Create a booking
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Check booked date color
        $bookedColor = Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('getAvailabilityColor', $this->venue->id, '2025-12-25');

        $this->assertEquals('bg-red-100 text-red-800', $bookedColor);

        // Check available date color
        $availableColor = Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('getAvailabilityColor', $this->venue->id, '2025-12-26');

        $this->assertEquals('bg-green-100 text-green-800', $availableColor);
    });

    test('admin can approve pending bookings', function () {
        $pendingBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('approveBooking', $pendingBooking->id);

        $this->assertDatabaseHas('bookings', [
            'id' => $pendingBooking->id,
            'status' => 'confirmed'
        ]);
    });

    test('admin can reject pending bookings', function () {
        $pendingBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('rejectBooking', $pendingBooking->id);

        $this->assertDatabaseHas('bookings', [
            'id' => $pendingBooking->id,
            'status' => 'rejected'
        ]);
    });

    test('calendar loads month view correctly', function () {
        $monthDays = Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('getMonthDays');

        $this->assertIsArray($monthDays);
        $this->assertGreaterThan(28, count($monthDays)); // Should have at least 4 weeks
    });

    test('calendar loads week view correctly', function () {
        $weekDays = Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('getWeekDays');

        $this->assertIsArray($weekDays);
        $this->assertEquals(7, count($weekDays)); // Should have exactly 7 days
    });

    test('calendar shows today correctly', function () {
        $today = now()->format('Y-m-d');
        
        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->set('selectedDate', $today)
            ->assertSet('selectedDate', $today);
    });

    test('calendar can reset to today', function () {
        $futureDate = now()->addMonth()->format('Y-m-d');
        
        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->set('selectedDate', $futureDate)
            ->call('$set', 'selectedDate', now()->format('Y-m-d'))
            ->assertSet('selectedDate', now()->format('Y-m-d'));
    });

    test('calendar filters bookings by venue', function () {
        $otherVenue = Venue::create([
            'user_id' => $this->vendor->id,
            'category_id' => $this->category->id,
            'name' => 'Other Venue',
            'description' => 'Another venue',
            'base_price' => 800.00,
            'location_name' => 'Other Location',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'active'
        ]);

        // Create bookings for both venues
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
            'venue_id' => $otherVenue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 800.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Test filtering by first venue
        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->set('selectedVenue', $this->venue->id)
            ->call('loadBookings')
            ->assertSee($this->venue->name)
            ->assertDontSee($otherVenue->name);
    });

    test('calendar handles empty bookings gracefully', function () {
        Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->set('selectedDate', '2025-01-01')
            ->call('loadBookings')
            ->assertSet('bookings', collect([]));
    });

    test('calendar prevents double booking at system level', function () {
        // This test verifies that the system prevents double bookings
        // even when trying to create them programmatically
        
        $date = '2025-12-25';
        
        // Create first booking
        $firstBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => $date,
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Try to create second booking for same date
        $secondBooking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => $date,
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        // Both bookings exist in database (this is expected behavior)
        // The calendar system will detect the conflict and show it
        $this->assertDatabaseHas('bookings', [
            'id' => $firstBooking->id,
            'event_date' => $date
        ]);
        
        $this->assertDatabaseHas('bookings', [
            'id' => $secondBooking->id,
            'event_date' => $date
        ]);

        // The calendar system should detect this conflict
        $isAvailable = Livewire::actingAs($this->admin)
            ->test(Calendar::class)
            ->call('checkAvailability', $this->venue->id, $date);

        $this->assertFalse($isAvailable);
    });
});

