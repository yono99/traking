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

        // Kategori dan status berdasarkan unit
        $statuses = [
            'verifikator' => ['PROSES VERIFIKASI', 'PROSES VERIFIKASI LANJUTAN', 'PROSES VERIFIKASI CROSSCHECK'],
            'pengukuran' => [

                'PROSES MEMPERBAHARUI',
                'PROSES ALIH MEDIA SUEL',
            ],
            'bukutanah' => ['PROSES CARI BT', 'PROSES ALIH MEDIA BTEL'],
            'sps' => ['PROSES SPS'],
            'bensus' => ['PROSES BENSUS', 'PROSES INFO'],
            'pelaksana' => [
                'PROSES PELAKSANA',
                'PROSES PELAKSANA BUAT CATATAN',
                'PROSES CETAK SERTEL'
            ],
            'pelaksana_bn' => [
                'PROSES PELAKSANA',
                'PROSES PELAKSANA BUAT CATATAN',
                'PROSES CETAK SERTEL'
            ],
            'pelaksana_ph' => [
                'PROSES PELAKSANA',
                'PROSES PELAKSANA BUAT CATATAN',
                'PROSES CETAK SERTEL'
            ],
            'pelaksana_roya' => [
                'PROSES PELAKSANA',
                'PROSES PELAKSANA BUAT CATATAN',
                'PROSES CETAK SERTEL'
            ],
            'pelaksana_ph_ruko' => [
                'PROSES PELAKSANA',
                'PROSES PELAKSANA BUAT CATATAN',
                'PROSES CETAK SERTEL'
            ],
            'pelaksana_sk' => [
                'PROSES PELAKSANA',
                'PROSES PELAKSANA BUAT CATATAN',
                'PROSES CETAK SERTEL'
            ],
            'pengesahan' => ['PROSES PENGESAHAN ALIH MEDIA BTEL'],
            'paraf' => ['PROSES PARAF'],
            'TTE_PRODUK_LAYANAN' => ['PROSES TTE'],
            'LOKET_PENYERAHAN' => ['DI PROSES'],
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
                'FORWARD PELAKSANA',
                'SELESAI INFO DISPOSISI',
            ],
            'pelaksana' => [
                "FORWARD PARAF",
                "FORWARD ALIH MEDIA SUEL",
                "FORWARD LOKET PENYERAHAN",
            ],
            'pelaksana_bn' => [
                "FORWARD PARAF",
                "FORWARD ALIH MEDIA SUEL",
                "FORWARD LOKET PENYERAHAN",
            ],
            'pelaksana_ph' => [
                "FORWARD PARAF",
                "FORWARD ALIH MEDIA SUEL",
                "FORWARD LOKET PENYERAHAN",
            ],
            'pelaksana_roya' => [
                "FORWARD PARAF",
                "FORWARD ALIH MEDIA SUEL",
                "FORWARD LOKET PENYERAHAN",
            ],
            'pelaksana_ph_ruko' => [
                "FORWARD PARAF",
                "FORWARD ALIH MEDIA SUEL",
                "FORWARD LOKET PENYERAHAN",
            ],
            'pelaksana_sk' => [
                "FORWARD PARAF",
                "FORWARD ALIH MEDIA SUEL",
                "FORWARD LOKET PENYERAHAN",
            ],
            'pengukuran' => [
                'FORWARD VERIFIKASI LANJUTAN',
                'FORWARD ALIH MEDIA BTEL',
                
            ],
            'bukutanah' => [
                'FORWARD VERIFIKATOR CEK SYARAT',
                'FORWARD PENGESAHAN ALIH MEDIA BTEL',
                
            ],
            'pengesahan' => ['FORWARD PARAF'],
            'paraf' => ['FORWARD TTE PRODUK LAYANAN'],
            'TTE_PRODUK_LAYANAN' => ['FORWARD PELAKSANA CETAK SERTEL'],
            'LOKET_PENYERAHAN' => ['SELESAI DISERAHKAN'],
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
