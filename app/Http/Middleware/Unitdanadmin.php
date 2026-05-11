<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Unitdanadmin
{
    public function handle(Request $request, Closure $next, ...$units)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userUnit = strtolower(trim(Auth::user()->unit));
        $userRole = strtolower(trim(Auth::user()->role));

        $allowedUnits = ['admin', 'loket'];

        // ✅ Cek unit ATAU role secara terpisah
        if (!in_array($userUnit, $allowedUnits) && !in_array($userRole, $allowedUnits)) {
            abort(403, 'Kamu tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}