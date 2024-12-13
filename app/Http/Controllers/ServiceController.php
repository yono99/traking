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
                'SELESAI TTE',
              
            ])->count();

            // Hitung jumlah berdasarkan status yang sudah selesai (contoh status selesai)
            $countProses = Service::whereIn('status', [

                'PROSES VERIFIKASI',
                'PROSES MEMPERBAHARUI REVISI',
                'PROSES MEMPERBAHARUI',
                'PROSES ALIH MEDIA SUEL',
                'PROSES CARI BT',
                'PROSES ALIH MEDIA BTEL',
                'PROSES SPS',
                'PROSES BENSUS',
                'PROSES PENGESAHAN ALIH MEDIA BTEL',
                'PROSES PARAF',
                'PROSES TTE',
                'FORWARD TTE PRODUK LAYANAN',
                'FORWARD PARAF',
                'FORWARD PENGESAHAN ALIH MEDIA BTEL',
                'FORWARD SELESAI REVISI',
                'FORWARD QC SELESAI ALIH MEDIA',
                'FORWARD BENSUS',
                'FORWARD SPS',
                'FORWARD ALIH MEDIA BTEL',
                'FORWARD CARI BT',
                'FORWARD ALIH MEDIA SUEL',
                'FORWARD PENGUKURAN',
                'FORWARD PENGUKURAN REVISI',
                'FORWARD VERIFIKATOR CEK SYARAT',
                'FORWARD VERIFIKATOR',
                
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
