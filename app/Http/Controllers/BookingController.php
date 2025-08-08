<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function bookingManagement(){

        return view('pages.client.booking-management');
    }

    public function pendingBooking(){

        return view( 'pages.client.pending-booking');
    }

    public function paymentIssues(){

        return view('pages.client.payment-issue');
    }
}
