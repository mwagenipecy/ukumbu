<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('welcome-client-page');
});

// Debug route to test middleware (remove in production)
Route::get('/debug-middleware', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }
    
    $user = auth()->user();
    return [
        'user' => $user->name,
        'role' => $user->role,
        'isAdmin' => $user->isAdmin(),
        'isClient' => $user->isClient(),
        'isVendor' => $user->isVendor(),
        'canAccessAdmin' => $user->isAdmin(),
        'shouldRedirectTo' => $user->isAdmin() ? 'admin dashboard' : 'user dashboard'
    ];
})->middleware('auth');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'admin.access',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});



///// client website section 
    // welcome page 
Route::get('welcome-client-page',[WebController::class,'welcomeClientPage'])->name('welcome.client.page');


/// view venues details 
Route::get('view-venue-details/{id}',[WebController::class,'viewVenueDetails'])->name('view.venue.details');


// Admin-only routes (Categories Management)
Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::get('categories-management',[WebController::class,'categoriesManagement'])->name('categories.management');
});


// Admin-only routes (Venue Management)
// Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::get('venue-management',[WebController::class,'venueManagement'])->name('venue.management');
// });


// Admin-only routes (Services Management)
 Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::get('service-form',[WebController::class,'serviceForm'])->name('admin.services.create');
    Route::get('service-form/{id}',[WebController::class,'serviceEditForm'])->name('service.form.edit');
 });


Route::get('services-management',[WebController::class,'servicesManagement'])->name('services.management');
Route::get('view-service-details/{id}',[WebController::class,'viewServiceDetails'])->name('view.service.details');






// Admin-only routes (User Management)
Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::get('user-management',[UserManagementController::class,'userManagement'])->name('user.management');
    Route::get('view-user-details/{id}', [UserManagementController::class, 'viewUserDetails'])->name('view.user.details');
});



// Admin-only routes (Review Management)
Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::get('review-management',[ReviewController::class,'reviewManagement'])->name('review.manage');
    Route::get('admin/reviews/export',[ReviewController::class,'exportReviews'])->name('admin.reviews.export');
});


// Admin-only routes (Booking Management)
 Route::middleware(['auth', 'admin.access'])->group(function () {
  

    Route::get('pending-booking',[BookingController::class,'pendingBooking'])->name('pending.booking');
Route::get('show-booking/{id}',[BookingController::class,'pendingBooking'])->name('admin.bookings.show');
Route::get('booking-invoice/{booking}',[BookingController::class,'pendingBooking'])->name('admin.bookings.invoice');
Route::get('payment-issues', [BookingController::class, 'paymentIssues'])->name('payment.issues');


});



Route::get('booking-management',[BookingController::class,'bookingManagement'])->name('booking.management');




// Admin Calendar Route (Admin Access Only)
Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::get('admin/calendar', function () {
        return view('livewire.admin.calendar');
    })->name('admin.calendar');
});

// User Dashboard Routes (Client & Vendor Access)
Route::middleware(['auth'])->group(function () {
    Route::get('user/dashboard', function () {
        return view('pages.user-dashboard');
    })->name('user.dashboard');
    
    Route::get('user/booking/{id}', function ($id) {
        return view('pages.client.booking-details', ['id' => $id]);
    })->name('user.booking.details');
});

// Vendor Routes (Client & Vendor Access)
Route::middleware(['auth'])->group(function () {
    Route::get('vendor/dashboard', function () {
        return view('pages.vendor-dashboard');
    })->name('vendor.dashboard');

    Route::get('vendor/pending', function () {
        return view('pages.vendor.pending');
    })->name('vendor.pending');
});

// Admin Routes (Admin Access Only)
Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::get('admin/dashboard', function () {
        return view('pages.admin.dashboard');
    })->name('admin.dashboard');
    
    // Admin Venue Management Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('venues', function () {
            return view('pages.admin.venue-management');
        })->name('venues.index');
        
        Route::get('services', function () {
            return view('pages.admin.services-management');
        })->name('services.index');
        
        Route::get('services/create', function () {
            return view('pages.client.service-form');
        })->name('admin.services.create.form');
    });
});
