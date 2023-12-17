<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isAuthorized
{
    public function handle(Request $request, Closure $next)
    {
        //check if user is admin or Employee
        if (Auth::check() && (Auth::user()->role == 1 || Auth::user()->role == 2) ) {

            return $next($request);
        }

        return response()->json(['status'=>'faild','msg'=>'Unauthorized action.'],403) ;
    }
}
