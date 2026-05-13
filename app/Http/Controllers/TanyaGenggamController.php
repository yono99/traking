<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Activity;

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

        $user = Auth::user();
        $unit = $user->unit;

        $service = Service::findOrFail($validatedData['service_id']);

        $statusMapping = $this->getUpdateStatusByUnit($unit);
        if (!isset($statusMapping[$service->status])) {
            return response()->json(['message' => 'Status tidak valid untuk unit Anda.'], 400);
        }

        $oldStatus = $service->status;
        $service->status = $statusMapping[$service->status];
        $service->save();

        $this->logActivity($service->id, $user->id, $oldStatus, $service->status);

        return response()->json(['message' => 'Berhasil dimasukan kedalam inventory']);
    }

    private function getUpdateStatusByUnit($unit)
    {
        return [
            'loket' => [
                'FORWARD LOKET' => 'PROSES LOKET',
            ],
            'bukutanah' => [
                'FORWARD BUKU TANAH' => 'PROSES BUKU TANAH',
                'FORWARD VALIDASI BUKU TANAH' => 'PROSES VALIDASI BUKU TANAH',
            ],
            'pengukuran' => [
                'FORWARD PENGUKURAN' => 'PROSES PENGUKURAN',
                'FORWARD VALIDASI BIDANG' => 'PROSES VALIDASI BIDANG',
            ],
            'loket_penyerahan' => [
                'FORWARD LOKET PENYERAHAN' => 'PROSES LOKET PENYERAHAN',
            ],
        ][$unit] ?? [];
    }

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