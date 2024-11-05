<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Subscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->subscribed('premium_plan')) {

            return $next($request);
        }

        return redirect('subscription/create');
    }
}
