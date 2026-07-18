<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Allow logout to prevent users from getting trapped
            if ($request->routeIs('logout')) {
                return $next($request);
            }

            // Force Password Change First
            if ($user->must_change_password) {
                if (!$request->routeIs('profile.change_password.*')) {
                    return redirect()->route('profile.change_password.view');
                }
            }
            // Then Force Profile Completion if user is a student
            elseif (!$user->profile_completed && $user->hasRole('student')) {
                if (!$request->routeIs('profile.complete.*')) {
                    return redirect()->route('profile.complete.view');
                }
            }
            // If they are on the change password/complete forms but already finished them, redirect to dashboard
            else {
                if ($request->routeIs('profile.change_password.*')) {
                    return redirect()->route('dashboard');
                }

                if ($request->routeIs('profile.complete.*')) {
                    // Allow if manager granted edit permission
                    if ($user->hasRole('student') && $user->student && $user->student->can_edit_profile) {
                        return $next($request);
                    }
                    return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}
