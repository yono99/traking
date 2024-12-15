<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
 use App\Models\LandBook;
 use Illuminate\Support\Facades\DB;
 use Inertia\Inertia;
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
            'bensus' => ['PROSES BENSUS', 'PROSES INFO DISPOSISI'],
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
        try {
            DB::beginTransaction();

            $service = Service::with('landBook')->findOrFail($serviceId);

            $service->update([
                'status' => $request->status,
                'PNBP' => $request->PNBP,
                'nomor_hp' => $request->nomor_hp,
                'remarks' => $request->remarks,
                'name'  => $request->name,
            ]);

            if ($service->landBook) {
                $service->landBook->update([
                    'nomer_hak' => $request->nomer_hak,
                    'jenis_hak' => $request->jenis_hak,
                    'desa_kecamatan' => $request->desa_kecamatan,
                    'status_alih_media' => $request->status_alih_media,
                ]);
            }
            // Log aktivitas jika diperlukan
            $this->logActivity($service->id, Auth::id(), $service->getOriginal('status'), $request->status);

            DB::commit();

            return response()->json([
                'message' => 'Data berhasil diupdate',
                'service' => $service->fresh(['landBook'])
            ]);
        }  catch (\Exception $e) {}

        $user = Auth::user(); // Pengguna yang login
        $userUnit = $user->unit; // Unit pengguna

        // Cari layanan berdasarkan ID
        $service = Service::findOrFail($serviceId); // Lempar 404 jika tidak ditemukan
        $oldStatus = $service->status; // Simpan status lama

        // Ambil status baru dari request
        $newStatus = $request->input('status');

        // Validasi apakah status baru valid untuk unit pengguna
        $allowedStatuses = $this->getAllowedStatusesByUnit($userUnit);
        $validated = $request->validate([
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'PNBP' => 'nullable|string',
            'nomor_hp' => 'nullable|string',
            'nomer_hak' => 'nullable|string',
            'jenis_hak' => 'nullable|string',
            'desa_kecamatan' => 'nullable|string',
            'status_alih_media' => 'nullable|string',
        ]);
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
        $service = Service::findOrFail($serviceId);
        $service->update($validated);

        return response()->json(['message' => 'Status berhasil diperbarui']);
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
