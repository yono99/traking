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
        // dd($nomorHak, $userUnit);

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
        // dd($request->all());
        try {
            $request->validate([
                'service_id' => 'required|integer|exists:services,id',
            ]);

            $serviceId = $request->input('service_id');
            $userUnit = Auth::user()->unit;

            $service = Service::find($serviceId);
            if (!$service) {
                return response()->json(['message' => 'Service tidak ditemukan.'], 404);
            }

            $statusUpdate = $this->getUpdateStatusByUnit($userUnit);
            if (!$statusUpdate) {
                return response()->json(['message' => 'Status tidak valid untuk unit pengguna.'], 400);
            }

            $service->status = $statusUpdate;
            $service->save();

            return response()->json(['message' => 'Status berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui status', ['exception' => $e]);
            return response()->json(['message' => 'Terjadi kesalahan internal pada server.'], 500);
        }
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
