<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFoodSubscriptionStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasRole('student') && auth()->user()->student) {
            $subscription = \App\Models\FoodSubscription::where('student_id', auth()->user()->student->id)
                ->where('status', 'active')
                ->first();

            if ($subscription) {
                $warning = $subscription->checkAndAutoSuspend();
                if ($warning) {
                    // Use now() instead of flash() to ensure it appears in the CURRENT request
                    session()->now('warning', $warning);
                }
            }
        }

        return $next($request);
    }
}
