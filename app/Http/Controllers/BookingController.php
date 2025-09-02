<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function bookingManagement(){
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return view('pages.client.booking-management'); // Admin booking management
        } elseif ($user->role === 'vendor') {
            return view('pages.vendor.booking-management'); // Vendor booking management
        } else {
            // Client booking management (via user dashboard)
            return redirect()->route('user.dashboard');
        }
    }

    public function pendingBooking(){

        return view( 'pages.client.pending-booking');
    }

    public function paymentIssues(){

        return view('pages.client.payment-issue');
    }
}
