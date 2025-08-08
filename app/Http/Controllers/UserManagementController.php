<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function userManagement(){

        return view('pages.client.user-management');
    }


    public function viewUserDetails($id){

        $user=User::findOrFail($id);

        return view('pages.client.view-user', ['user' => $user]);
    }



}
