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
        $user = Auth::user(); // Mengambil user yang login
        $unit = $user->unit; // Unit berdasarkan user yang login

        // Kategori dan status
        $statuses = [
            'verifikator' => ['PROSES VERIFIKASI', 'PROSES VERIFIKASI'],
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

        // Ambil data berdasarkan unit dan status
        $activities = Activity::whereHas('user', function ($query) use ($unit) {
            $query->where('unit', $unit); // Filter berdasarkan kolom 'unit' di tabel 'users'
        })
        ->where(function ($query) use ($statuses) {
            foreach ($statuses as $key => $statusGroup) {
                $query->orWhereIn('status', $statusGroup); // Filter berdasarkan status di tabel 'activities'
            }
        })
        ->with(['service.landBook', 'user']) // Lazy load relasi jika diperlukan
        ->get();

        // Kirim ke Inertia
        return inertia('Inventory', [
            'activities' => $activities,
        ]);

    }
    /**
     * Perbarui status dan catat aktivitas.
     */
    public function updateStatus(Request $request, $serviceId)
    {
        $user = Auth::user(); // Data pengguna yang login
        $unit = $user->unit; // Unit pengguna

        // Cari layanan berdasarkan ID
        $service = Service::findOrFail($serviceId);
        $oldStatus = $service->status; // Simpan status lama

        // Ambil status baru dari request
        $newStatus = $request->input('status');

        // Validasi apakah status baru valid untuk unit pengguna
        $allowedStatuses = $this->getAllowedStatusesByUnit($unit);

        if (!in_array($newStatus, $allowedStatuses)) {
            return response()->json(['message' => 'Status tidak valid untuk unit Anda.'], 400);
        }

        // Update status pada tabel services
        $service->update([
            'status' => $newStatus,
        ]);

        // Catat aktivitas ke tabel activities
        $this->logActivity($serviceId, $user->id, $oldStatus, $newStatus);

        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'newStatus' => $newStatus,
        ]);
    }

    private function getAllowedStatusesByUnit($unit)
    {
        // Mapping tombol yang diizinkan untuk setiap unit
        return [
            'verifikator' => [
                'FORWARD PENGUKURAN',
                'FORWARD CARI BT',
                'FORWARD BENSUS DISPOSISI',
                'FORWARD SPS',
                'FORWARD BENSUS',
            ],
            'sps' => [
                'FORWARD BENSUS',
            ],
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
            'pengesahan' => [
                'FORWARD PARAF',
            ],
            'paraf' => [
                'FORWARD TTE PRODUK LAYANAN',
            ],
            'TTE_PRODUK_LAYANAN' => [
                'SELESAI TTE',
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
