<?php

use App\Models\User;
use App\Models\Venue;
use App\Models\Category;
use App\Models\Booking;
use App\Models\Payment;
use App\Livewire\Client\UserDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('User Dashboard Tests', function () {
    
    beforeEach(function () {
        $this->user = User::factory()->create(['role' => 'client']);
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

    test('user can access dashboard', function () {
        $response = $this->actingAs($this->user)
            ->get('/user/dashboard');
        
        $response->assertStatus(200);
    });

    test('dashboard shows user stats', function () {
        // Create some bookings and payments
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'method' => 'card',
            'amount' => 1000.00,
            'status' => 'completed'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->assertSee('Total Bookings')
            ->assertSee('1')
            ->assertSee('Upcoming')
            ->assertSee('1')
            ->assertSee('Total Spent')
            ->assertSee('TSh 1,000');
    });

    test('dashboard shows upcoming bookings', function () {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->assertSee($this->venue->name)
            ->assertSee('Dec 25, 2025')
            ->assertSee('Confirmed');
    });

    test('dashboard can switch tabs', function () {
        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->call('switchTab', 'bookings')
            ->assertSet('activeTab', 'bookings')
            ->call('switchTab', 'payments')
            ->assertSet('activeTab', 'payments')
            ->call('switchTab', 'overview')
            ->assertSet('activeTab', 'overview');
    });

    test('bookings tab shows all user bookings', function () {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->call('switchTab', 'bookings')
            ->assertSee($this->venue->name)
            ->assertSee('TSh 1,000')
            ->assertSee('Confirmed');
    });

    test('payments tab shows payment history', function () {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'method' => 'card',
            'amount' => 1000.00,
            'status' => 'completed'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->call('switchTab', 'payments')
            ->assertSee($this->venue->name)
            ->assertSee('TSh 1,000')
            ->assertSee('Completed');
    });

    test('user can view booking details', function () {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->call('showBookingDetails', $booking->id)
            ->assertSet('showBookingModal', true)
            ->assertSet('selectedBooking.id', $booking->id);
    });

    test('user can close booking modal', function () {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->call('showBookingDetails', $booking->id)
            ->call('closeBookingModal')
            ->assertSet('showBookingModal', false)
            ->assertSet('selectedBooking', null);
    });

    test('user can cancel pending booking', function () {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->call('switchTab', 'bookings')
            ->call('cancelBooking', $booking->id);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled'
        ]);
    });

    test('user cannot cancel confirmed booking from another user', function () {
        $otherUser = User::factory()->create(['role' => 'client']);
        $booking = Booking::create([
            'user_id' => $otherUser->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->call('cancelBooking', $booking->id);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'pending'
        ]);
    });

    test('dashboard shows recent activity', function () {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->assertSee('Booked ' . $this->venue->name)
            ->assertSee('Event on Dec 25, 2025');
    });

    test('dashboard handles empty states gracefully', function () {
        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->assertSee('No upcoming bookings')
            ->assertSee('Start exploring venues and services')
            ->assertSee('No recent activity');
    });

    test('dashboard shows correct booking counts', function () {
        // Create different types of bookings
        Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-01-15',
            'total_amount' => 800.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-02-20',
            'total_amount' => 1200.00,
            'status' => 'cancelled',
            'payment_status' => 'refunded'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->assertSee('3') // Total bookings
            ->assertSee('1') // Upcoming (confirmed)
            ->assertSee('1') // Completed
            ->assertSee('1'); // Cancelled
    });

    test('dashboard shows payment statistics', function () {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'method' => 'card',
            'amount' => 1000.00,
            'status' => 'completed'
        ]);

        Livewire::actingAs($this->user)
            ->test(UserDashboard::class)
            ->assertSee('TSh 1,000') // Total spent
            ->assertSee('0'); // Pending payments
    });
});
