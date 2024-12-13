<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Mengambil user yang login
        $userUnit = $user->unit; // Unit pengguna

        // status berdasarkan unit
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
            'pelaksana_bn',
            'pelaksana_ph',
            'pelaksana_roya',
            'pelaksana_ph_ruko',
            'pelaksana_sk' => ['PROSES STAGING', 'PROSES CATATAN PELAKSANA', 'PROSES STAGING AKHIR'],
            'pengesahan' => ['PROSES PENGESAHAN ALIH MEDIA BTEL'],
            'paraf' => ['PROSES PARAF'],
            'TTE_PRODUK_LAYANAN' => ['PROSES TTE'],
        ];

        // Ambil layanan berdasarkan status dan unit pengguna
        $services = Service::whereIn('status', $statuses[$userUnit] ?? [])
            ->with(['landBook']) // Lazy load relasi jika diperlukan
            ->get();
        // dd($activities);
        // Kirim data ke Inertia
        return inertia('Inventory', [
            'services' => $services,
            'user' => $user,
        ]);
    }

    public function updateStatus(Request $request, $serviceId)
    {
        $user = Auth::user(); // Pengguna yang login
        $userUnit = $user->unit; // Unit pengguna

        // Cari layanan berdasarkan ID
        $service = Service::findOrFail($serviceId); // Lempar 404 jika tidak ditemukan
        $oldStatus = $service->status; // Simpan status lama

        // Ambil status baru dari request
        $newStatus = $request->input('status');

        // Validasi apakah status baru valid untuk unit pengguna
        $allowedStatuses = $this->getAllowedStatusesByUnit($userUnit);

        if (!in_array($newStatus, $allowedStatuses)) {
            return response()->json(['message' => 'Status tidak valid untuk unit Anda.'], 400);
        }

        // Update status pada tabel services
        $service->update(['status' => $newStatus]);

        // Log aktivitas ke tabel activities
        $this->logActivity($service->id, $user->id, $oldStatus, $newStatus);

        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'newStatus' => $newStatus,
        ]);
    }

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
                'FORWARD PELAKSANA ',
                'FORWARD BENSUS DISPOSISI UPDATE SELESAI',
            ],
            'pelaksana',
            'pelaksana_bn',
            'pelaksana_ph',
            'pelaksana_roya',
            'pelaksana_ph_ruko',
            'pelaksana_sk' => ['FORWARD ALIH MEDIA SUEL', 'FORWARD PARAF', 'FORWARD LOKET PENYERAHAN','FORWARD PENGESAHAN ALIH MEDIA BTEL'],
            'pengukuran' => [
                'FORWARD VERIFIKATOR',
                'FORWARD ALIH MEDIA BTEL',
                
            ],
            'bukutanah' => [
                'FORWARD VERIFIKATOR CEK SYARAT',
                'FORWARD QC SELESAI ALIH MEDIA',
                'FORWARD SELESAI REVISI',
            ],
            'pengesahan' => ['FORWARD CATATAN PELAKSANA'],
            'paraf' => ['FORWARD TTE PRODUK LAYANAN'],
            'TTE_PRODUK_LAYANAN' => ['SELESAI TTE'],
            'loket_penyerahan' => ['BERHASIL DI SERAHKAN KE PPAT ATAU PEMOHON'],
        ][$unit] ?? [];
    }

    private function logActivity($serviceId, $userId, $oldStatus, $newStatus)
    {
        \App\Models\Activity::create([
            'service_id' => $serviceId,
            'user_id' => $userId,
            'status' => $newStatus,
            'remarks' => "Status berubah dari '$oldStatus' menjadi '$newStatus'",
        ]);
    }
}
