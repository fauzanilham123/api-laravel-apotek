<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Resources\SendError;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Cashier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next,)
    {
        $user = Auth::user();

        if (!$user || $user->role !== "kasir") {
            return new SendError(403, "error", "forbidden");
        }

        return $next($request);
    }
}