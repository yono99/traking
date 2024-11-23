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
        $user = Auth::user(); // Data pengguna yang login
        $unit = $user->unit; // Unit pengguna (e.g., verifikator, pengukuran, dll.)
        $nomerHak = $request->input('nomer_hak'); // Nomor Hak yang dicari

        // Validasi input
        if (!$nomerHak || !is_numeric($nomerHak)) {
            return response()->json(['message' => 'Nomor hak harus berupa angka.'], 400);
        }

        // Mapping status yang sesuai untuk unit
        $statusByUnit = $this->getStatusByUnit($unit);

        // Query data berdasarkan nomor hak dan status
        $landBooks = LandBook::where('nomer_hak', $nomerHak)->first();

        if (!$landBooks) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $services = Service::where('land_book_id', $landBooks->id)
            ->whereIn('status', $statusByUnit) // Hanya status yang belum diperbarui
            ->get();

        return response()->json([
            'land_book' => $landBooks,
            'services' => $services,
            'total_services' => $services->count(),
        ]);
    }

    private function getStatusByUnit($unit)
    {
        return [
            'verifikator' => ['FORWARD VERIFIKATOR', 'FORWARD VERIFIKATOR CEK SYARAT'],
            'pengukuran' => ['FORWARD PENGUKURAN REVISI', 'FORWARD PENGUKURAN', 'FORWARD ALIH MEDIA SUEL'],
            'bukutanah' => ['FORWARD CARI BT', 'FORWARD ALIH MEDIA BTEL'],
            'sps' => ['FORWARD SPS'],
            'bensus' => ['FORWARD BENSUS'],
            'QC' => ['FORWARD QC SELESAI ALIH MEDIA'],
            'pengesahan' => ['FORWARD PENGESAHAN ALIH MEDIA BTEL'],
            'paraf' => ['FORWARD PARAF'],
            'TTE_PRODUK_LAYANAN' => ['FORWARD TTE PRODUK LAYANAN'],
        ][$unit] ?? [];
    }


    public function updateStatus(Request $request)
    {
        $validatedData = $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $user = Auth::user(); // Data pengguna yang login
        $unit = $user->unit; // Unit pengguna (e.g., verifikator, pengukuran, dll.)

        $service = Service::findOrFail($validatedData['service_id']);

        // Cek apakah status yang sekarang sesuai dengan unit pengguna
        $statusMapping = $this->getUpdateStatusByUnit($unit);
        if (!isset($statusMapping[$service->status])) {
            return response()->json(['message' => 'Status tidak valid untuk unit Anda.'], 400);
        }

        // Simpan status lama sebelum diupdate
        $oldStatus = $service->status;

        // Perbarui status
        $service->status = $statusMapping[$service->status];
        $service->save();

        // Catat aktivitas ke tabel activities
        $this->logActivity($service->id, $user->id, $oldStatus, $service->status);

        return response()->json(['message' => 'Status berhasil diperbarui dan aktivitas dicatat!']);
    }

    private function getUpdateStatusByUnit($unit)
    {
        return [
            'verifikator' => [
                'FORWARD VERIFIKATOR' => 'PROSES VERIFIKASI',
                'FORWARD VERIFIKATOR CEK SYARAT' => 'PROSES VERIFIKASI',
            ],
            'pengukuran' => [
                'FORWARD PENGUKURAN REVISI' => 'PROSES MEMPERBAHARUI',
                'FORWARD PENGUKURAN' => 'PROSES MEMPERBAHARUI',
                'FORWARD ALIH MEDIA SUEL' => 'PROSES ALIH MEDIA SUEL',
            ],
            'bukutanah' => [
                'FORWARD CARI BT' => 'PROSES CARI BT',
                'FORWARD ALIH MEDIA BTEL' => 'PROSES ALIH MEDIA BTEL',
            ],
            'sps' => [
                'FORWARD SPS' => 'PROSES SPS',
            ],
            'bensus' => [
                'FORWARD BENSUS' => 'PROSES BENSUS',
            ],
            'QC' => [
                'FORWARD QC SELESAI ALIH MEDIA' => 'PROSES QC',
            ],
            'pengesahan' => [
                'FORWARD PENGESAHAN ALIH MEDIA BTEL' => 'PROSES PENGESAHAN ALIH MEDIA BTEL',
            ],
            'paraf' => [
                'FORWARD PARAF' => 'PROSES PARAF',
            ],
            'TTE_PRODUK_LAYANAN' => [
                'FORWARD TTE PRODUK LAYANAN' => 'PROSES TTE',
            ],
        ][$unit] ?? [];
    }

    /**
     * Log aktivitas ke tabel activities.
     */
    private function logActivity($serviceId, $userId, $oldStatus, $newStatus)
    {
        Activity::create([
            'service_id' => $serviceId,
            'user_id' => $userId,
            'status' => $newStatus,
            'remarks' => "Status berubah dari '$oldStatus' menjadi '$newStatus'",
        ]);
    }
}
