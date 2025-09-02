<?php

use App\Models\User;
use App\Models\Venue;
use App\Models\Service;
use App\Models\Category;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

describe('Payment and Review System End-to-End Tests', function () {
    
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

    test('client can make payment for booking', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending'
        ]);

        $paymentData = [
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'transaction_id' => 'TXN123456',
            'status' => 'completed'
        ];

        $response = $this->actingAs($this->client)
            ->post("/bookings/{$booking->id}/payments", $paymentData);
        
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'status' => 'completed'
        ]);

        // Check that booking payment status is updated
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => 'paid'
        ]);
    });

    test('payment failure updates booking status', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending'
        ]);

        $paymentData = [
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'transaction_id' => 'TXN123456',
            'status' => 'failed',
            'failure_reason' => 'Insufficient funds'
        ];

        $response = $this->actingAs($this->client)
            ->post("/bookings/{$booking->id}/payments", $paymentData);
        
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'status' => 'failed',
            'failure_reason' => 'Insufficient funds'
        ]);

        // Check that booking payment status remains pending
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => 'pending'
        ]);
    });

    test('admin can view payment issues', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending'
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'transaction_id' => 'TXN123456',
            'status' => 'failed',
            'failure_reason' => 'Insufficient funds'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/payment-issues');
        
        $response->assertStatus(200);
        $response->assertSee($this->client->name);
        $response->assertSee('Insufficient funds');
    });

    test('admin can resolve payment issues', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending'
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'transaction_id' => 'TXN123456',
            'status' => 'failed',
            'failure_reason' => 'Insufficient funds'
        ]);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/payments/{$payment->id}/resolve", [
                'resolution' => 'Client will pay via bank transfer'
            ]);
        
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'resolved'
        ]);
    });

    test('client can review venue after booking', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-01-15', // Past date
            'total_amount' => 1000.00,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        $reviewData = [
            'rating' => 5,
            'comment' => 'Amazing venue! Everything was perfect.',
            'review_type' => 'venue'
        ];

        $response = $this->actingAs($this->client)
            ->post("/venues/{$this->venue->id}/reviews", $reviewData);
        
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'reviewable_type' => Venue::class,
            'rating' => 5,
            'comment' => 'Amazing venue! Everything was perfect.'
        ]);
    });

    test('client can review service after booking', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-01-15', // Past date
            'total_amount' => 1500.00, // Including service
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        $reviewData = [
            'rating' => 4,
            'comment' => 'Great service, very professional.',
            'review_type' => 'service'
        ];

        $response = $this->actingAs($this->client)
            ->post("/services/{$this->service->id}/reviews", $reviewData);
        
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->client->id,
            'reviewable_id' => $this->service->id,
            'reviewable_type' => Service::class,
            'rating' => 4,
            'comment' => 'Great service, very professional.'
        ]);
    });

    test('client cannot review before event completion', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25', // Future date
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $reviewData = [
            'rating' => 5,
            'comment' => 'This should not be allowed',
            'review_type' => 'venue'
        ];

        $response = $this->actingAs($this->client)
            ->post("/venues/{$this->venue->id}/reviews", $reviewData);
        
        // Should fail validation
        $response->assertSessionHasErrors();
        
        $this->assertDatabaseMissing('reviews', [
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'comment' => 'This should not be allowed'
        ]);
    });

    test('admin can moderate reviews', function () {
        $review = Review::create([
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'reviewable_type' => Venue::class,
            'rating' => 1,
            'comment' => 'Inappropriate comment that needs moderation',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/reviews/{$review->id}/moderate", [
                'action' => 'approve',
                'moderator_notes' => 'Review approved after verification'
            ]);
        
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'status' => 'approved'
        ]);
    });

    test('admin can reject inappropriate reviews', function () {
        $review = Review::create([
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'reviewable_type' => Venue::class,
            'rating' => 1,
            'comment' => 'Inappropriate comment',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/reviews/{$review->id}/moderate", [
                'action' => 'reject',
                'moderator_notes' => 'Review contains inappropriate content'
            ]);
        
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'status' => 'rejected'
        ]);
    });

    test('review statistics are calculated correctly', function () {
        // Create multiple reviews
        Review::create([
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'reviewable_type' => Venue::class,
            'rating' => 5,
            'comment' => 'Excellent',
            'status' => 'approved'
        ]);

        Review::create([
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'reviewable_type' => Venue::class,
            'rating' => 4,
            'comment' => 'Very good',
            'status' => 'approved'
        ]);

        Review::create([
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'reviewable_type' => Venue::class,
            'rating' => 3,
            'comment' => 'Good',
            'status' => 'approved'
        ]);

        // Calculate average rating
        $averageRating = Review::where('reviewable_id', $this->venue->id)
            ->where('reviewable_type', Venue::class)
            ->where('status', 'approved')
            ->avg('rating');

        $this->assertEquals(4.0, round($averageRating, 1));
    });

    test('payment refund process works correctly', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'cancelled',
            'payment_status' => 'paid'
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'transaction_id' => 'TXN123456',
            'status' => 'completed'
        ]);

        // Process refund
        $refundData = [
            'refund_amount' => 1000.00,
            'refund_reason' => 'Event cancelled by client',
            'refund_method' => 'credit_card_refund'
        ];

        $response = $this->actingAs($this->admin)
            ->post("/admin/payments/{$payment->id}/refund", $refundData);
        
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'refunded'
        ]);

        // Check that booking payment status is updated
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => 'refunded'
        ]);
    });

    test('payment gateway integration test', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending'
        ]);

        // Simulate payment gateway response
        $gatewayResponse = [
            'success' => true,
            'transaction_id' => 'GTW_' . uniqid(),
            'amount' => 1000.00,
            'currency' => 'TZS',
            'status' => 'completed'
        ];

        $response = $this->actingAs($this->client)
            ->post("/bookings/{$booking->id}/process-payment", [
                'gateway_response' => $gatewayResponse
            ]);
        
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'transaction_id' => $gatewayResponse['transaction_id'],
            'status' => 'completed'
        ]);
    });

    test('review notification system works', function () {
        $review = Review::create([
            'user_id' => $this->client->id,
            'reviewable_id' => $this->venue->id,
            'reviewable_type' => Venue::class,
            'rating' => 5,
            'comment' => 'Excellent venue!',
            'status' => 'approved'
        ]);

        // Check if vendor is notified
        $response = $this->actingAs($this->vendor)
            ->get('/vendor/notifications');
        
        $response->assertStatus(200);
        $response->assertSee('New review received');
    });

    test('payment reminder system works', function () {
        $booking = Booking::create([
            'user_id' => $this->client->id,
            'venue_id' => $this->venue->id,
            'event_date' => '2025-12-25',
            'total_amount' => 1000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending',
            'created_at' => now()->subDays(7) // 7 days ago
        ]);

        // Check if payment reminder is sent
        $response = $this->actingAs($this->admin)
            ->get('/admin/payment-reminders');
        
        $response->assertStatus(200);
        $response->assertSee($this->client->name);
    });
});
