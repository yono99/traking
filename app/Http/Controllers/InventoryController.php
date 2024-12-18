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
        $user = Auth::user(); // Pengguna yang login
        $userUnit = $user->unit; // Unit pengguna

        // Validasi input dari request
       

        try {
            DB::beginTransaction(); // Mulai transaksi database
            $validated = $request->validate([
                'status' => 'required|string',
                'remarks' => 'nullable|string',
                'PNBP' => 'nullable|string',
                'nomor_hp' => 'nullable|string',
                'nomer_hak' => 'nullable|string',
                'jenis_hak' => 'nullable|string',
                'desa_kecamatan' => 'nullable|string',
                'status_alih_media' => 'nullable|string',
                'name' => 'nullable|string', // Pastikan validasi untuk kolom name
            ]);
            // Cari layanan berdasarkan ID
            $service = Service::with('landBook')->findOrFail($serviceId);
            $oldStatus = $service->status; // Simpan status lama
            $newStatus = $validated['status']; // Ambil status baru

           

            // Update status dan data lain pada tabel services
            $service->update([
                'status' => $newStatus,
                'PNBP' => $validated['PNBP'] ?? $service->PNBP,
                'nomor_hp' => $validated['nomor_hp'] ?? $service->nomor_hp,
                'remarks' => $validated['remarks'] ?? $service->remarks,
                'name' => $validated['name'] ?? $service->name ?? 'Default Name', // Fallback aman untuk kolom name
            ]);

            // Jika layanan memiliki relasi dengan landBook, update juga datanya
            if ($service->landBook) {
                $service->landBook->update([
                    'nomer_hak' => $validated['nomer_hak'] ?? $service->landBook->nomer_hak,
                    'jenis_hak' => $validated['jenis_hak'] ?? $service->landBook->jenis_hak,
                    'desa_kecamatan' => $validated['desa_kecamatan'] ?? $service->landBook->desa_kecamatan,
                    'status_alih_media' => $validated['status_alih_media'] ?? $service->landBook->status_alih_media,
                ]);
            }

            // Log aktivitas perubahan status
            $this->logActivity($service->id, Auth::id(), $oldStatus, $newStatus);

            DB::commit(); // Selesaikan transaksi jika tidak ada error

            return response()->json([
                'message' => 'Data berhasil diupdate',
                'service' => $service->fresh(['landBook']) // Muat ulang data dengan relasi
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika ada error
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengupdate data',
                'error' => $e->getMessage()
            ], 500);
        }
        // Validasi apakah status baru valid untuk unit pengguna
        // $allowedStatuses = $this->getAllowedStatusesByUnit($userUnit);
        // if (!in_array($newStatus, $allowedStatuses)) {
        //     return response()->json(['message' => 'Status tidak valid untuk unit Anda.'], 400);
        // }
    }

    // private function getAllowedStatusesByUnit($unit)
    // {
    //     return [
    //         'verifikator' => [
    //             'FORWARD PENGUKURAN',
    //             'FORWARD CARI BT',
    //             'FORWARD BENSUS DISPOSISI',
    //             'FORWARD SPS',
    //         ],
    //         'sps' => ['FORWARD BENSUS'],
    //         'bensus' => [
    //             'FORWARD PELAKSANA',
    //             'SELESAI INFO DISPOSISI',
    //         ],
    //         'pelaksana' => [
    //             "FORWARD PARAF",
    //             "FORWARD ALIH MEDIA SUEL",
    //             "FORWARD LOKET PENYERAHAN",
    //         ],
    //         'pelaksana_bn' => [
    //             "FORWARD PARAF",
    //             "FORWARD ALIH MEDIA SUEL",
    //             "FORWARD LOKET PENYERAHAN",
    //         ],
    //         'pelaksana_ph' => [
    //             "FORWARD PARAF",
    //             "FORWARD ALIH MEDIA SUEL",
    //             "FORWARD LOKET PENYERAHAN",
    //         ],
    //         'pelaksana_roya' => [
    //             "FORWARD PARAF",
    //             "FORWARD ALIH MEDIA SUEL",
    //             "FORWARD LOKET PENYERAHAN",
    //         ],
    //         'pelaksana_ph_ruko' => [
    //             "FORWARD PARAF",
    //             "FORWARD ALIH MEDIA SUEL",
    //             "FORWARD LOKET PENYERAHAN",
    //         ],
    //         'pelaksana_sk' => [
    //             "FORWARD PARAF",
    //             "FORWARD ALIH MEDIA SUEL",
    //             "FORWARD LOKET PENYERAHAN",
    //         ],
    //         'pengukuran' => [
    //             'FORWARD VERIFIKASI LANJUTAN',
    //             'FORWARD ALIH MEDIA BTEL',
                
    //         ],
    //         'bukutanah' => [
    //             'FORWARD VERIFIKATOR CEK SYARAT',
    //             'FORWARD PENGESAHAN ALIH MEDIA BTEL',
                
    //         ],
    //         'pengesahan' => ['FORWARD PARAF'],
    //         'paraf' => ['FORWARD TTE PRODUK LAYANAN'],
    //         'TTE_PRODUK_LAYANAN' => ['FORWARD PELAKSANA CETAK SERTEL'],
    //         'LOKET_PENYERAHAN' => ['SELESAI DISERAHKAN'],
    //     ][$unit] ?? [];
    // }

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
