<?php

namespace App\Http\Middleware;

use App\Models\AllSigned;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TreeAuthority
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $childId= $request->userId;
        if(Auth::id()===$childId){
            return $next($request);
        }

        $parentId = Auth::id(); // Assuming the parent ID is retrieved from the user model
        $allSigned = AllSigned::where('parent_id', $parentId)->where('child_id',$childId)->first();

        if (!$allSigned) {
            return response()->json(['status' => 'failed', 'message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
