<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user && $user->isVendor() && $user->isPending()) {
            return redirect()->route('vendor.pending');
        }

        if ($user && $user->isClient()) {
            return redirect()->to('/user/dashboard');
        }

        if ($user && $user->isVendor()) {
            return redirect()->route('vendor.dashboard');
        }

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(config('fortify.home'));
    }
}


