<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUnit
{
    public function handle(Request $request, Closure $next, $unit)
    {
        // Memeriksa apakah pengguna yang login memiliki role yang sesuai
        if (Auth::check() && Auth::user()->unit !== $unit) {
            return redirect()->route('dashboard')->with('error', 'Hanya admin yang bisa mengakses halaman ini.');
        }

        return $next($request);
    }
}
