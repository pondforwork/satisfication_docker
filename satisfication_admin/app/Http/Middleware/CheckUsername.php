<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUsername
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('username')) {
            // return redirect()->route('unauthorized');
            echo "Unauthorized" ;

        }

        return $next($request);
    }
}
