<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $nomerHak = $request->input('nomer_hak');
        $userUnit = Auth::user()->unit;

        $statusArray = $this->getStatusByUnit($userUnit);

        $services = Service::when(!empty($statusArray), function ($query) use ($statusArray) {
                return $query->whereIn('status', $statusArray);
            })
            ->with('landBook')
            ->when(!empty($nomerHak), function ($query) use ($nomerHak) {
                return $query->whereHas('LandBook', function ($subQuery) use ($nomerHak) {
                    $subQuery->where('nomer_hak', $nomerHak);
                });
            })
            ->get();

        return response()->json(['services' => $services]);
    }

    private function getStatusByUnit($unit)
    {
        return [
            'loket'            => ['FORWARD LOKET'],
            'bukutanah'        => ['FORWARD BUKU TANAH', 'FORWARD VALIDASI BUKU TANAH'],
            'pengukuran'       => ['FORWARD PENGUKURAN', 'FORWARD VALIDASI BIDANG'],
            'loket_penyerahan' => ['FORWARD LOKET PENYERAHAN'],
        ][$unit] ?? [];
    }
}