<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class VerifyJwtToken
{
    public function handle($request, Closure $next)
    {
        try {

            $token = JWTAuth::parseToken();
            $token->authenticate();

        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (TokenExpiredException $e ) {

            return response()->json(['error' => 'Token expired'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token not provided'], 401);
        }


        return $next($request);
    }
}
