<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function reviewManagement(){

        return view('pages.client.review-management');
    }


    public function exportReviews(){

        return view('pages.client.review-report');
    }
}
