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
        $nomorHak = $request->input('nomer_hak');

        // Validasi input
        if (!is_numeric($nomorHak)) {
            return response()->json(['message' => 'Nomer hak harus berupa angka.'], 400);
        }

        // Ambil data LandBook berdasarkan nomer_hak
        $landBooks = LandBook::where('nomer_hak', $nomorHak)->get();

        // Ambil data Services yang berhubungan
        $services = Service::whereIn('land_book_id', $landBooks->pluck('id'))->get();

        return response()->json([
            'landBooks' => $landBooks,
            'services' => $services,
            'totalItems' => $landBooks->count(),
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
        $validatedData = $request->validate([
            'service_id' => 'required|exists:services,id',
            'status' => 'required|string',
        ]);

        $service = Service::findOrFail($validatedData['service_id']);
        $service->remarks = $validatedData['status'];
        $service->save();

        return response()->json(['message' => 'Status berhasil diperbarui!']);
    }

    private function getUpdateStatusByUnit($unit)
    {
        $statuses = [
            'verifikator' => 'UPDATE PROSES VERIFIKASI',
            'pengukuran' => 'UPDATE PROSES MEMPERBAHARUI',
            'bukutanah' => 'UPDATE PROSES ALIH MEDIA BTEL',
            'sps' => 'UPDATE PROSES SPS',
            'bensus' => 'UPDATE PROSES BENSUS',
            'QC' => 'UPDATE PROSES QC',
            'pengesahan' => 'UPDATE PROSES PENGESAHAN ALIH MEDIA BTEL',
            'paraf' => 'UPDATE PROSES PARAF',
            'TTE_PRODUK_LAYANAN' => 'UPDATE PROSES TTE',
        ];

        return $statuses[$unit] ?? null;
    }
}
