<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Menangani request yang diberikan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        // Memeriksa apakah pengguna sudah login (terautentikasi)
        if (Auth::guard($guard)->check()) {
            return $next($request);  // Melanjutkan ke request berikutnya jika sudah login
        }

        // Jika belum login, arahkan ke halaman login
        return redirect()->route('login');
    }
}
