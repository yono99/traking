<?php
// app/Http/Middleware/CheckUnit.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUnit
{
    public function handle(Request $request, Closure $next, ...$units)
    {
        $userUnit = Auth::user()->unit;  // Ambil unit dari user yang sedang login

        if (!in_array($userUnit, $units)) {
            abort(403, 'You do not have access to this page');  // Menampilkan pesan 403 jika unit tidak cocok
        }

        return $next($request);
    }
}
