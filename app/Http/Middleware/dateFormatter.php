<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class dateFormatter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //reformat birthdate
        $b_day = $request->input('b_day');
        $b_month = $request->input('b_month');
        $b_year = $request->input('b_year');

        if ($b_day && $b_month && $b_year) {
            $birthday = $b_day . '/' . $b_month . '/' . $b_year;
            $request->merge(['birthday' => $birthday]);
        }
        //reformat man7 date
        $m_day = $request->input('m_day');
        $m_month = $request->input('m_month');
        $m_year = $request->input('m_year');

        if ($m_day && $m_month && $m_year) {
            $man7_history = $m_day . '/' . $m_month . '/' . $m_year;
            $request->merge(['man7_history' => $man7_history]);
        }
        return $next($request);
    }
}
