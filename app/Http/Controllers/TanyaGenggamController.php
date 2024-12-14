<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandBook;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;
class TanyaGenggamController extends Controller
{
    public function index()
    {
        return Inertia::render('TanyaGenggam');
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
                'FORWARD VERIFIKATOR CEK SYARAT' => 'PROSES VERIFIKASI CROSSCHECK',
                'FORWARD VERIFIKASI LANJUTAN' => 'PROSES VERIFIKASI LANJUTAN'
            ],
            'pengukuran' => [
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
                'FORWARD BENSUS DISPOSISI' => 'PROSES INFO',
            ],
            'pelaksana'=> [
                'FORWARD PELAKSANA' => 'PROSES PELAKSANA',
                'FORWARD PELAKSANA BUAT CATATAN' => 'PROSES PELAKSANA BUAT CATATAN',
                'FORWARD PELAKSANA CETAK SERTEL' => "PROSES CETAK SERTEL",
            ],
            'pelaksana_bn' => [
                'FORWARD PELAKSANA' => 'PROSES PELAKSANA',
                'FORWARD PELAKSANA BUAT CATATAN' => 'PROSES PELAKSANA BUAT CATATAN',
                'FORWARD PELAKSANA CETAK SERTEL' => "PROSES CETAK SERTEL",
            ],
            'pelaksana_ph' => [
                'FORWARD PELAKSANA' => 'PROSES PELAKSANA',
                'FORWARD PELAKSANA BUAT CATATAN' => 'PROSES PELAKSANA BUAT CATATAN',
                'FORWARD PELAKSANA CETAK SERTEL' => "PROSES CETAK SERTEL",
            ],
            'pelaksana_roya' => [
                'FORWARD PELAKSANA' => 'PROSES PELAKSANA',
                'FORWARD PELAKSANA BUAT CATATAN' => 'PROSES PELAKSANA BUAT CATATAN',
                'FORWARD PELAKSANA CETAK SERTEL' => "PROSES CETAK SERTEL",
            ],
            'pelaksana_ph_ruko' => [
                'FORWARD PELAKSANA' => 'PROSES PELAKSANA',
                'FORWARD PELAKSANA BUAT CATATAN' => 'PROSES PELAKSANA BUAT CATATAN',
                'FORWARD PELAKSANA CETAK SERTEL' => "PROSES CETAK SERTEL",
            ],
            'pelaksana_sk' => [
                'FORWARD PELAKSANA' => 'PROSES PELAKSANA',
                'FORWARD PELAKSANA BUAT CATATAN' => 'PROSES PELAKSANA BUAT CATATAN',
                'FORWARD PELAKSANA CETAK SERTEL' => "PROSES CETAK SERTEL",
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
            'LOKET_PENYERAHAN' => [
                'FORWARD LOKET PENYERAHAN' => 'DI PROSES',
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
