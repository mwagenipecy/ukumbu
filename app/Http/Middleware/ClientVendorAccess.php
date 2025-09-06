<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientVendorAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = auth()->user();

        // Check if user has client or vendor role
        if (!$user->isClient() && !$user->isVendor()) {
            // If user is admin, redirect to admin dashboard
            if ($user->isAdmin()) {
                return redirect()->route('dashboard')->with('error', 'Access denied. Admin users should use the admin dashboard.');
            }
            
            // If user has no valid role, redirect to home with error
            return redirect()->route('welcome.client.page')->with('error', 'Access denied. You do not have permission to access this area.');
        }

        // Check if user account is approved (optional - you can remove this if not needed)
        if ($user->isPending()) {
            return redirect()->route('welcome.client.page')->with('warning', 'Your account is pending approval. Please wait for admin approval.');
        }

        return $next($request);
    }
}
