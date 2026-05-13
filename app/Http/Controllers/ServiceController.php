<?php

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
        $service->update(['status' => $request->status]);
        return redirect()->route('inventory.index');
    }

    public function getServiceCounts()
    {
        try {
            $countSelesai = Service::whereIn('status', [
                'SELESAI DISERAHKAN',
            ])->count();

            $countProses = Service::whereIn('status', [
                'FORWARD LOKET',
                'PROSES LOKET',
                'FORWARD BUKU TANAH',
                'PROSES BUKU TANAH',
                'FORWARD PENGUKURAN',
                'PROSES PENGUKURAN',
                'FORWARD VALIDASI BIDANG',
                'PROSES VALIDASI BIDANG',
                'FORWARD VALIDASI BUKU TANAH',
                'PROSES VALIDASI BUKU TANAH',
                'FORWARD LOKET PENYERAHAN',
                'PROSES LOKET PENYERAHAN',
            ])->count();

            return response()->json([
                'countSelesaiTTE' => $countSelesai,
                'countProses' => $countProses,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch counts.'], 500);
        }
    }

    public function show(Service $service)
    {
        $service->load('land_book');
        return response()->json([
            'service' => $service,
            'landBook' => $service->land_book,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        try {
            DB::beginTransaction();

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

            $service->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'],
                'PNBP' => $validated['PNBP'],
                'nomor_hp' => $validated['nomor_hp'],
            ]);

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
                'service' => $service->fresh('land_book'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal mengupdate data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function dataProses()
    {
        try {
            $dataProses = Service::with('landBook')->whereIn('status', [
                'FORWARD LOKET',
                'PROSES LOKET',
                'FORWARD BUKU TANAH',
                'PROSES BUKU TANAH',
                'FORWARD PENGUKURAN',
                'PROSES PENGUKURAN',
                'FORWARD VALIDASI BIDANG',
                'PROSES VALIDASI BIDANG',
                'FORWARD VALIDASI BUKU TANAH',
                'PROSES VALIDASI BUKU TANAH',
                'FORWARD LOKET PENYERAHAN',
                'PROSES LOKET PENYERAHAN',
            ])->get();

            return inertia('Process/ProcessData', [
                'dataProses' => $dataProses,
                'countProses' => $dataProses->count(),
                'title' => 'Data Proses',
                'subtitle' => 'Daftar proses yang sedang berlangsung',
                'atEmpty' => 'Belum ada data yang sedang diproses',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Gagal mendapatkan data', 'error' => $th->getMessage()], 500);
        }
    }

    public function dataProsesTte()
    {
        try {
            $dataProses = Service::with('landBook')->whereIn('status', [
                'SELESAI DISERAHKAN',
            ])->get();

            return inertia('Process/ProcessData', [
                'dataProses' => $dataProses,
                'countProses' => $dataProses->count(),
                'title' => 'Data Selesai',
                'subtitle' => 'Daftar berkas yang telah selesai diserahkan',
                'atEmpty' => 'Belum ada data yang selesai',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Gagal mendapatkan data', 'error' => $th->getMessage()], 500);
        }
    }
}