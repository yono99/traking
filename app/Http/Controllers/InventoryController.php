<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Pengguna yang login
        $userId = $user->id; // ID pengguna
        $userUnit = $user->unit; // Unit pengguna

        // Definisi status berdasarkan unit pengguna
        $statuses = [
            'verifikator' => ['PROSES VERIFIKASI'],
            'pengukuran' => [
                'PROSES MEMPERBAHARUI REVISI',
                'PROSES MEMPERBAHARUI',
                'PROSES ALIH MEDIA SUEL',
            ],
            'bukutanah' => ['PROSES CARI BT', 'PROSES ALIH MEDIA BTEL'],
            'sps' => ['PROSES SPS'],
            'bensus' => ['PROSES BENSUS'],
            'QC' => ['PROSES QC'],
            'pengesahan' => ['PROSES PENGESAHAN ALIH MEDIA BTEL'],
            'paraf' => ['PROSES PARAF'],
            'TTE_PRODUK_LAYANAN' => ['PROSES TTE'],
        ];

        // Ambil data dari tabel services berdasarkan relasi dengan activities
        $services = Service::whereIn('status', $statuses[$userUnit] ?? [])
            ->whereHas('activities', function ($query) use ($userId) {
                $query->where('user_id', $userId); // Filter berdasarkan pengguna yang login
            })
            ->with(['landBook', 'activities']) // Relasi jika diperlukan
            ->get();

        // Kirim data ke Inertia
        return inertia('Inventory', [
            'services' => $services, // Kirim data services langsung ke Vue
        ]);
    }


    /**
     * Perbarui status layanan pada tabel services.
     */
    public function updateStatus(Request $request, $serviceId)
    {
        $user = Auth::user(); // Ambil pengguna yang login
        $userId = $user->id; // Ambil ID pengguna
        $userUnit = $user->unit; // Ambil unit pengguna

        // Cari layanan berdasarkan ID
        $service = Service::where('id', $serviceId)
            ->where('user_id', $userId) // Pastikan layanan milik pengguna
            ->firstOrFail();

        $oldStatus = $service->status; // Simpan status lama
        $newStatus = $request->input('status'); // Ambil status baru dari request

        // Validasi status baru berdasarkan unit pengguna
        $allowedStatuses = $this->getAllowedStatusesByUnit($userUnit);

        if (!in_array($newStatus, $allowedStatuses)) {
            return response()->json(['message' => 'Status tidak valid untuk unit Anda.'], 400);
        }

        // Perbarui status di tabel services
        $service->update(['status' => $newStatus]);

        // Log aktivitas perubahan status
        $this->logActivity($service->id, $userId, $oldStatus, $newStatus);

        // Berikan respons sukses
        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'newStatus' => $newStatus,
        ]);
    }

    /**
     * Mengembalikan status yang diizinkan berdasarkan unit pengguna.
     */
    private function getAllowedStatusesByUnit($unit)
    {
        return [
            'verifikator' => [
                'FORWARD PENGUKURAN',
                'FORWARD CARI BT',
                'FORWARD BENSUS DISPOSISI',
                'FORWARD SPS',
            ],
            'sps' => ['FORWARD BENSUS'],
            'bensus' => [
                'FORWARD ALIH MEDIA SUEL',
                'FORWARD BENSUS DISPOSISI UPDATE SELESAI',
            ],
            'QC' => [
                'FORWARD PENGESAHAN ALIH MEDIA BTEL',
                'FORWARD BUKU TANAH REVISI',
                'FORWARD PENGUKURAN REVISI',
            ],
            'pengukuran' => [
                'FORWARD VERIFIKATOR',
                'FORWARD ALIH MEDIA BTEL',
                'FORWARD SELESAI REVISI',
            ],
            'bukutanah' => [
                'FORWARD VERIFIKATOR CEK SYARAT',
                'FORWARD QC SELESAI ALIH MEDIA',
                'FORWARD SELESAI REVISI',
            ],
            'pengesahan' => ['FORWARD PARAF'],
            'paraf' => ['FORWARD TTE PRODUK LAYANAN'],
            'TTE_PRODUK_LAYANAN' => ['SELESAI TTE'],
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
