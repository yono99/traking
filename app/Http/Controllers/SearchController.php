<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\LandBook;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{

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

        return response()->json([
            // 'landBooks' => $landBooks,
            'services' => $services,
        ]);
    }

    private function getStatusByUnit($unit)
    {
        switch ($unit) {
            case 'verifikator':
                return ['FORWARD VERIFIKATOR', 'FORWARD VERIFIKATOR CEK SYARAT', 'FORWARD VERIFIKASI LANJUTAN'];
            case 'pengukuran':
                return ['FORWARD PENGUKURAN', 'FORWARD ALIH MEDIA SUEL'];
            case 'bukutanah':
                return ['FORWARD CARI BT', 'FORWARD ALIH MEDIA BTEL'];
            case 'sps':
                return ['FORWARD SPS'];
            case 'bensus':
                return ['FORWARD BENSUS', 'FORWARD BENSUS DISPOSISI'];
            case 'pelaksana':
            case 'pelaksana':
            case 'pelaksana_bn':
            case 'pelaksana_ph':
            case  'pelaksana_roya':
            case  'pelaksana_ph_ruko':
            case 'pelaksana_sk':
                return ['FORWARD PELAKSANA', 'FORWARD PELAKSANA BUAT CATATAN', 'FORWARD PELAKSANA CEKTAK SERTEL'];
            case 'pengesahan':
                return ['FORWARD PENGESAHAN ALIH MEDIA BTEL'];
            case 'paraf':
                return ['FORWARD PARAF'];
            case 'TTE_PRODUK_LAYANAN':
                return ['FORWARD TTE PRODUK LAYANAN'];
            case 'LOKET_PENYERAHAN':
                return ['FORWARD LOKET PENYERAHAN'];
            default:
                return [];
        }
    }
}
