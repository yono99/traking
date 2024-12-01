<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandBook;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TanyaGenggamController extends Controller
{
    public function index()
    {
        return Inertia::render('TanyaGenggam');
    }
    public function search(Request $request)
    {
        $nomerHak = $request->input('nomer_hak');
        $userUnit = Auth::user()->unit;

        // Tentukan status berdasarkan unit user
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


        // Ambil data dari tabel land_books dan services
        // $landBooks = LandBook::where('nomer_hak','=', $nomorHak)->get();
        // $services = Service::whereIn('status', $statusArray)->get();
        // $services = Service::when(!empty($statusArray), function ($query) use ($statusArray) {
        //     return $query->whereIn('status', $statusArray);
        // })->get();

        // if ($landBooks->isEmpty() && $services->isEmpty()) {
        //     return response()->json([
        //         'message' => 'No records found.',
        //     ], 404);
        // }
        //  dd($services);

        return response()->json([
            // 'landBooks' => $landBooks,
            'services' => $services,
        ]);
    }

    private function getStatusByUnit($unit)
    {
        switch ($unit) {
            case 'verifikator':
                return ['FORWARD VERIFIKATOR', 'FORWARD VERIFIKATOR CEK SYARAT'];
            case 'pengukuran':
                return ['FORWARD PENGUKURAN REVISI', 'FORWARD PENGUKURAN', 'FORWARD ALIH MEDIA SUEL'];
            case 'bukutanah':
                return ['FORWARD CARI BT', 'FORWARD ALIH MEDIA BTEL'];
            case 'sps':
                return ['FORWARD SPS'];
            case 'bensus':
                return ['FORWARD BENSUS'];
            case 'pelaksana_bn':
            case 'pelaksana_ph':
            case 'pelaksana_roya':
            case 'pelaksana_ph_ruko':
            case 'pelaksana_sk':
                return ['FORWARD PELAKSANA'];
            case 'QC':
            case 'QC_bn':
            case 'QC_ph':
            case 'QC_roya':
            case 'QC_ph_ruko':
            case 'QC_sk':
                return ['FORWARD QC SELESAI ALIH MEDIA'];
            case 'pengesahan':
                return ['FORWARD PENGESAHAN ALIH MEDIA BTEL'];
            case 'paraf':
                return ['FORWARD PARAF'];
            case 'TTE_PRODUK_LAYANAN':
                return ['FORWARD TTE PRODUK LAYANAN'];
            default:
                return [];
        }
    }
}
