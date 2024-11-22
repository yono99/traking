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
        $nomorHak = $request->input('nomer_hak');
        $userUnit = Auth::user()->unit;

        // Tentukan status berdasarkan unit user
        $statusArray = $this->getStatusByUnit($userUnit);

        // dd($userUnit,$statusArray);

        // Ambil data dari tabel land_books dan services
        $landBooks = LandBook::where('nomer_hak', 'like', '%' . $nomorHak . '%')->get();
        // $services = Service::whereIn('status', $statusArray)->get();
        $services = Service::when(!empty($statusArray), function ($query) use ($statusArray) {
            return $query->whereIn('status', $statusArray);
        })->get();

        if ($landBooks->isEmpty() && $services->isEmpty()) {
            return response()->json([
                'message' => 'No records found.',
            ], 404);
        }
        // dd($services);

        return response()->json([
            'landBooks' => $landBooks,
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
            case 'QC':
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
