<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function welcomeClientPage(){
        return view('pages.client.welcome-client-page');
    }



    public function viewVenueDetails($id){

       
        return view('pages.client.view-venue-details',['id' => $id]);
    }



    public function categoriesManagement(){


        return view('pages.client.categories-management');
    }


    public function venueManagement(){

        return view('pages.client.venue-management');
    }
    

    public function servicesManagement(){

        return view('pages.client.services-management');
    }



    public function viewServiceDetails($id){

        $service=Service::findOrFail($id);
        return view('pages.client.view-service-details',['service' => $service]);
    }
    public function serviceForm(){

        return view('pages.client.service-form');
    }
    


    // edit form service

    public function serviceEditForm($id){

        $service=Service::findOrFail($id);  
        return view('pages.client.service-form-edit',['service' => $service]);
    }




}
