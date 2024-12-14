<?php
// app/Http/Controllers/ServiceController.php
namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update([
            'status' => $request->status
        ]);

        return redirect()->route('inventory.index');
    }

    // Metode untuk menghitung jumlah berdasarkan status
    public function getServiceCounts()
    {
        try {
            // Menghitung jumlah seluruh data Service
            $countSelesaiTTE = Service::whereIn('status', [
                'FORWARD PELAKSANA CEKTAK SERTEL',

            ])->count();

            // Hitung jumlah berdasarkan status yang sudah selesai (contoh status selesai)
            $countProses = Service::whereIn('status', [

                'PROSES VERIFIKASI',
                'PROSES VERIFIKASI LANJUTAN',
                'PROSES MEMPERBAHARUI',
                'PROSES ALIH MEDIA SUEL',
                'PROSES CARI BT',
                'PROSES ALIH MEDIA BTEL',
                'PROSES SPS',
                'PROSES BENSUS',
                'PROSES PELAKSANA',
                'PROSES PELAKSANA BUAT CATATAN',
                'PROSES CETAK SERTEL',
                'PROSES PENGESAHAN ALIH MEDIA BTEL',
                'PROSES PARAF',
                'PROSES TTE',
                'DI PROSES',
                'FORWARD PENGUKURAN',
                'FORWARD CARI BT',
                'FORWARD BENSUS DISPOSISI',
                'FORWARD SPS',
                'FORWARD BENSUS',
                'FORWARD PELAKSANA',
                'FORWARD PARAF',
                'FORWARD ALIH MEDIA SUEL',
                'FORWARD LOKET PENYERAHAN',
                'FORWARD VERIFIKATOR',
                'FORWARD ALIH MEDIA BTEL',
                'FORWARD SELESAI REVISI',
                'FORWARD VERIFIKATOR CEK SYARAT',
                'FORWARD PENGESAHAN ALIH MEDIA BTEL',
                'FORWARD TTE PRODUK LAYANAN',
                'FORWARD PELAKSANA CETAK SERTEL',
            ])->count();

            // Kirimkan data sebagai JSON response
            return response()->json([

                'countSelesaiTTE' => $countSelesaiTTE,
                'countProses' => $countProses
            ]);
        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan error
            return response()->json(['error' => 'Failed to fetch counts.'], 500);
        }
    }
}
