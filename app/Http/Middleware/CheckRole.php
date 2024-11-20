<?php
 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Memeriksa apakah pengguna yang login memiliki role yang sesuai
        if (Auth::check() && Auth::user()->role !== $role) {
            return redirect()->route('dashboard')->with('error', 'Hanya admin yang bisa mengakses halaman ini.');
        }

        return $next($request);
    }
}
