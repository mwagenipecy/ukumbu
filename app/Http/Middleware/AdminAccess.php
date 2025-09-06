<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
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

        // Check if user has admin role
        if (!$user->isAdmin()) {
            // Log the access attempt for debugging
            \Log::info('Non-admin user attempted to access admin area', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'requested_url' => $request->url()
            ]);
            
            // If user is client or vendor, redirect to user dashboard
            if ($user->isClient() || $user->isVendor()) {
                return redirect()->route('user.dashboard')->with('error', 'Access denied. Admin access required for this area.');
            }
            
            // If user has no valid role, redirect to home with error
            return redirect()->route('welcome.client.page')->with('error', 'Access denied. Admin access required for this area.');
        }

        return $next($request);
    }
}
