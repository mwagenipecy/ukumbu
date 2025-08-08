<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('welcome-client-page');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
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


//// categories management 
Route::get('categories-management',[WebController::class,'categoriesManagement'])->name('categories.management');


///// venue management 
Route::get('venue-management',[WebController::class,'venueManagement'])->name('venue.management');


//// services managements 
Route::get('services-management',[WebController::class,'servicesManagement'])->name('services.management');
Route::get('view-service-details/{id}',[WebController::class,'viewServiceDetails'])->name('view.service.details');
Route::get('service-form',[WebController::class,'serviceForm'])->name('admin.services.create');
Route::get('service-form/{id}',[WebController::class,'serviceEditForm'])->name('service.form.edit');





//// user management 
Route::get('user-management',[UserManagementController::class,'userManagement'])->name('user.management');
Route::get('view-user-details/{id}', [UserManagementController::class, 'viewUserDetails'])->name('view.user.details');



//// review management 
Route::get('review-management',[ReviewController::class,'reviewManagement'])->name('review.manage');
Route::get('admin/reviews/export',[ReviewController::class,'exportReviews'])->name('admin.reviews.export');


/// boooking management 
Route::get('booking-management',[BookingController::class,'bookingManagement'])->name('booking.management');
Route::get('pending-booking',[BookingController::class,'pendingBooking'])->name('pending.booking');
Route::get('show-booking/{id}',[BookingController::class,'pendingBooking'])->name('admin.bookings.show');
Route::get('booking-invoice/{booking}',[BookingController::class,'pendingBooking'])->name('admin.bookings.invoice');
Route::get('payment-issues', [BookingController::class, 'paymentIssues'])->name('payment.issues');
