<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role == 1) {

            return $next($request);
        }

        return response()->json(['status'=>'faild','msg'=>'Unauthorized action.'],403) ;
    }
}
