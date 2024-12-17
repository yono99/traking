<?php
// app/Http/Controllers/ServiceController.php
namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\LandBook;

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

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
            $countSelesaiTTE = Activity::whereIn('status', [
                'FORWARD PELAKSANA CETAK SERTEL',

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
    public function show(Service $service)
    {
        // Load relationship dengan land_book
        $service->load('land_book');

        return response()->json([
            'service' => $service,
            'landBook' => $service->land_book
        ]);
    }
    public function update(Request $request, Service $service)
    {
        try {
            DB::beginTransaction();

            // Validate request
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

            // Update service
            $service->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'],
                'PNBP' => $validated['PNBP'],
                'nomor_hp' => $validated['nomor_hp'],
                'name' => $validated['name'],

            ]);

            // Update or create land book
            if ($service->land_book) {
                $service->land_book->update([
                    'nomer_hak' => $validated['nomer_hak'],
                    'jenis_hak' => $validated['jenis_hak'],
                    'desa_kecamatan' => $validated['desa_kecamatan'],
                    'status_alih_media' => $validated['status_alih_media'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data berhasil diupdate',
                'service' => $service->fresh('land_book')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal mengupdate data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function dataProses(){
        // return inertia('Process/ProcessData');
        try {
            $dataProses = Service::with('landBook')->whereIn('status', [
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
            ])->get();
            $countProses = $dataProses->count();
            $title = 'Data Proses';
            $subtitle = 'Daftar proses yang sedang berlangsung';
            $atEmpty = 'Belum ada data yang sedang diproses';
            // dd($dataProses, $countProses);
            return inertia('Process/ProcessData', [
                'dataProses' => $dataProses,
                'countProses' => $countProses,
                'title' => $title,
                'subtitle' => $subtitle,
                'atEmpty' => $atEmpty
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Gagal mendapatkan data',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function dataProsesTte(){
        // return inertia('Process/ProcessData');
        try {
            $dataProses = Service::whereIn('status', [
                'FORWARD PELAKSANA CEKTAK SERTEL',
            ])->get();

            $countProses = $dataProses->count();
            $title = 'Data Selesai TTE';
            $subtitle = 'Daftar proses selesai TTE';
            $atEmpty = 'Belum ada data yang selesai TTE';
            // dd($dataProses, $countProses);
            return inertia('Process/ProcessData', [
                'dataProses' => $dataProses,
                'countProses' => $countProses,
                'title' => $title,
                'subtitle' => $subtitle,
                'atEmpty' => $atEmpty
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Gagal mendapatkan data',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
