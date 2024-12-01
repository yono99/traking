<?php
// app/Http/Middleware/CheckUnit.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUnit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  $units
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$units)
    {
        // Daftar unit yang diizinkan
        $allowedUnits = [
            'verifikator',
            'pengukuran',
            'bukutanah',
            'sps',
	    'bensus',
            'QC',
            'pengesahan',
            'paraf',
            'TTE_PRODUK_LAYANAN',
        ];

        // Ambil unit dari pengguna yang sedang login
        $userUnit = Auth::user()->unit; // Pastikan kolom "unit" ada di tabel users

        // Cek apakah unit pengguna termasuk dalam daftar yang diizinkan
        if (!in_array($userUnit, $allowedUnits)) {
            // Jika unit tidak termasuk dalam daftar, tampilkan pesan 403
            abort(403, 'kamu tidak memiliki akses ke halaman ini.');
        }

        // Jika unit valid, lanjutkan request
        return $next($request);
    }
}
