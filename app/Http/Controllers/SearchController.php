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

        if (empty($statusByUnit)) {
            return response()->json(['message' => 'Unit Anda tidak memiliki status yang relevan.'], 400);
        }

        // Query data buku tanah berdasarkan nomor hak (pencocokan tepat)
        $landBooks = LandBook::where('nomer_hak', '==',$nomerHak)->get();

        if ($landBooks->isEmpty()) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        // Filter buku tanah berdasarkan layanan dengan status relevan
        $filteredLandBooks = $landBooks->filter(function ($landBook) use ($statusByUnit) {
            return Service::where('land_book_id', $landBook->id)
                ->whereIn('status', $statusByUnit) // Hanya layanan dengan status relevan
                ->exists(); // Cek apakah layanan relevan tersebut ada
        });

        if ($filteredLandBooks->isEmpty()) {
            return response()->json(['message' => 'Tidak ada layanan relevan untuk buku tanah ini.'], 404);
        }

        // Ambil layanan relevan untuk buku tanah yang difilter
        $filteredServices = Service::whereIn('land_book_id', $filteredLandBooks->pluck('id')->toArray())
        ->whereIn('status', $statusByUnit) // Filter layanan berdasarkan status relevan
        ->get();


        // Return response
        return response()->json([
            'land_books' => $filteredLandBooks->values(), // Reset key indexing
            'services' => $filteredServices,
            'total_services' => $filteredServices->count(),
        ]);
    }

    private function getStatusByUnit($unit)
    {
        return [
            'verifikator' => ['FORWARD VERIFIKATOR', 'FORWARD VERIFIKATOR CEK SYARAT'],
            'pengukuran' => ['FORWARD PENGUKURAN REVISI', 'FORWARD PENGUKURAN', 'FORWARD ALIH MEDIA SUEL',],
            'bukutanah' => ['FORWARD CARI BT', 'FORWARD ALIH MEDIA BTEL','FORWARD ALIH MEDIA REVISI'],
            'sps' => ['FORWARD SPS'],
            'bensus' => ['FORWARD BENSUS, FORWARD BENSUS DISPOSISI'],
            'QC' => ['FORWARD QC SELESAI ALIH MEDIA', 'FORWARD QC, FORWARD SELESAI REVISI', 'FORWARD QC, FORWARD SELESAI REVISI'],
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
                'FORWARD PENGUKURAN REVISI' => 'PROSES MEMPERBAHARUI REVISI',
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
                'FORWARD SELESAI REVISI ' => 'PROSES QC'
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
