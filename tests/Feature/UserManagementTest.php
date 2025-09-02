<?php

use App\Models\User;
use App\Models\Venue;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

describe('User Management End-to-End Tests', function () {
    
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->client = User::factory()->create(['role' => 'client']);
        $this->vendor = User::factory()->create(['role' => 'vendor']);
        
        $this->category = Category::create([
            'name' => 'Wedding Venue',
            'type' => 'venue'
        ]);
    });

    test('admin can view all users', function () {
        $response = $this->actingAs($this->admin)
            ->get('/user-management');
        
        $response->assertStatus(200);
        $response->assertSee($this->client->name);
        $response->assertSee($this->vendor->name);
        $response->assertSee($this->admin->name);
    });

    test('admin can view user details', function () {
        $response = $this->actingAs($this->admin)
            ->get("/view-user-details/{$this->client->id}");
        
        $response->assertStatus(200);
        $response->assertSee($this->client->name);
        $response->assertSee($this->client->email);
    });

    test('admin can change user role', function () {
        $response = $this->actingAs($this->admin)
            ->patch("/admin/users/{$this->client->id}/role", [
                'role' => 'vendor'
            ]);
        
        $this->assertDatabaseHas('users', [
            'id' => $this->client->id,
            'role' => 'vendor'
        ]);
    });

    test('admin can deactivate user', function () {
        $response = $this->actingAs($this->admin)
            ->patch("/admin/users/{$this->client->id}/deactivate");
        
        $this->assertDatabaseHas('users', [
            'id' => $this->client->id,
            'email_verified_at' => null
        ]);
    });

    test('client cannot access admin areas', function () {
        $response = $this->actingAs($this->client)
            ->get('/user-management');
        
        $response->assertStatus(403);
    });

    test('vendor cannot access admin areas', function () {
        $response = $this->actingAs($this->vendor)
            ->get('/user-management');
        
        $response->assertStatus(403);
    });

    test('unauthenticated users cannot access protected areas', function () {
        $response = $this->get('/user-management');
        
        $response->assertRedirect('/login');
    });

    test('admin can view user statistics', function () {
        // Create some test data
        $venue = Venue::create([
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

        $response = $this->actingAs($this->admin)
            ->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('User Statistics');
    });

    test('user profile information can be updated', function () {
        $newName = 'Updated Name';
        
        $response = $this->actingAs($this->client)
            ->patch('/user/profile-information', [
                'name' => $newName,
                'email' => $this->client->email
            ]);
        
        $this->assertDatabaseHas('users', [
            'id' => $this->client->id,
            'name' => $newName
        ]);
    });

    test('user password can be updated', function () {
        $newPassword = 'newpassword123';
        
        $response = $this->actingAs($this->client)
            ->patch('/user/password', [
                'current_password' => 'password',
                'password' => $newPassword,
                'password_confirmation' => $newPassword
            ]);
        
        $response->assertSessionHasNoErrors();
    });

    test('user can delete their account', function () {
        $response = $this->actingAs($this->client)
            ->delete('/user', [
                'password' => 'password'
            ]);
        
        $this->assertDatabaseMissing('users', [
            'id' => $this->client->id
        ]);
    });

    test('admin can view user activity', function () {
        $response = $this->actingAs($this->admin)
            ->get("/admin/users/{$this->client->id}/activity");
        
        $response->assertStatus(200);
    });

    test('role-based access control works correctly', function () {
        // Test client access
        $clientResponse = $this->actingAs($this->client)
            ->get('/venue-management');
        $clientResponse->assertStatus(200);
        
        // Test vendor access
        $vendorResponse = $this->actingAs($this->vendor)
            ->get('/venue-management');
        $vendorResponse->assertStatus(200);
        
        // Test admin access
        $adminResponse = $this->actingAs($this->admin)
            ->get('/venue-management');
        $adminResponse->assertStatus(200);
    });

    test('user registration creates correct role', function () {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'client'
        ];

        $response = $this->post('/register', $userData);
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'client'
        ]);
    });

    test('user can view their own profile', function () {
        $response = $this->actingAs($this->client)
            ->get('/profile');
        
        $response->assertStatus(200);
        $response->assertSee($this->client->name);
        $response->assertSee($this->client->email);
    });

    test('user cannot view other user profiles', function () {
        $response = $this->actingAs($this->client)
            ->get("/view-user-details/{$this->vendor->id}");
        
        $response->assertStatus(403);
    });
});

