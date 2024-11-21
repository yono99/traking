<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\LandBook;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        
        $nomorHak = $request->input('nomer_hak');
        $userUnit = Auth::user()->unit;

        // Tambahkan debugging
        dd($nomorHak, $userUnit);

        // Tentukan status berdasarkan unit user
        $statusArray = $this->getStatusByUnit($userUnit);

        // Ambil data dari tabel land_books dan services
        $landBooks = LandBook::where('nomer_hak', 'like', '%' . $nomorHak . '%')->get();
        $services = Service::whereIn('status', $statusArray)->get();

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

    public function updateStatus(Request $request)

    {

        $serviceId = $request->input('service_id');
        $userUnit = Auth::user()->unit;

        $service = Service::find($serviceId);
        $statusUpdate = $this->getUpdateStatusByUnit($userUnit);

        if ($service && $statusUpdate) {
            $service->status = $statusUpdate;
            $service->save();
        }

        return response()->json(['message' => 'Status berhasil diperbarui.']);
    }

    private function getUpdateStatusByUnit($unit)
    {
        switch ($unit) {
            case 'verifikator':
                return 'UPDATE PROSES VERIFIKASI';
            case 'pengukuran':
                return 'UPDATE PROSES MEMPERBAHARUI';
            case 'bukutanah':
                return 'UPDATE PROSES ALIH MEDIA BTEL';
            case 'sps':
                return 'UPDATE PROSES SPS';
            case 'bensus':
                return 'UPDATE PROSES BENSUS';
            case 'QC':
                return 'UPDATE PROSES QC';
            case 'pengesahan':
                return 'UPDATE PROSES PENGESAHAN ALIH MEDIA BTEL';
            case 'paraf':
                return 'UPDATE PROSES PARAF';
            case 'TTE_PRODUK_LAYANAN':
                return 'UPDATE PROSES TTE';
            default:
                return null;
        }
    }
}
