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
    $user = Auth::user();
    $userUnit = $user->unit; // string dari kolom DB

    $statuses = [
        'loket' => ['PROSES LOKET'],
        'bukutanah' => ['PROSES BUKU TANAH', 'PROSES VALIDASI BUKU TANAH'],
        'pengukuran' => ['PROSES PENGUKURAN', 'PROSES VALIDASI BIDANG'],
        'loket_penyerahan' => ['PROSES LOKET PENYERAHAN'],
    ];

    abort_if(is_null($userUnit), 403, 'User belum memiliki unit.');

    $services = Service::whereIn('status', $statuses[$userUnit] ?? [])
        ->with(['landBook'])
        ->get();

    return inertia('Inventory', [
        'services' => $services,
        'user' => $user,
    ]);
}
    public function updateStatus(Request $request, $serviceId)
    {
        $user = Auth::user();

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
                'name' => 'nullable|string',
                'Noberkas' => 'nullable|string',
            ]);

            $service = Service::with('landBook')->findOrFail($serviceId);
            $oldStatus = $service->status;
            $newStatus = $validated['status'];

            $service->update([
                'status' => $newStatus,
                'PNBP' => $validated['PNBP'] ?? $service->PNBP,
                'nomor_hp' => $validated['nomor_hp'] ?? $service->nomor_hp,
                'remarks' => $validated['remarks'] ?? $service->remarks,
                'name' => $validated['name'] ?? $service->name,
                'Noberkas' => $validated['Noberkas'] ?? $service->Noberkas,
            ]);

            if ($service->landBook) {
                $service->landBook->update([
                    'nomer_hak' => $validated['nomer_hak'] ?? $service->landBook->nomer_hak,
                    'jenis_hak' => $validated['jenis_hak'] ?? $service->landBook->jenis_hak,
                    'desa_kecamatan' => $validated['desa_kecamatan'] ?? $service->landBook->desa_kecamatan,
                    'status_alih_media' => $validated['status_alih_media'] ?? $service->landBook->status_alih_media,
                ]);
            }

            $this->logActivity($service->id, Auth::id(), $oldStatus, $newStatus);

            DB::commit();

            return response()->json([
                'message' => 'Data berhasil diupdate',
                'service' => $service->fresh(['landBook'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengupdate data',
                'error' => $e->getMessage()
            ], 500);
        }
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