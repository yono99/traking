<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;

class UnitExport
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getData()
    {
        $currentUserId = Auth::user()->id;
        return \App\Models\Service::with('landBook')

            ->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->get()
            ->where('user_id', $currentUserId)
            ->map(function ($service) {
                return [
                    'Jenis Hak' => $service->landBook->jenis_hak ?? '-',
                    'Nomor Hak' => $service->landBook->nomer_hak ?? '-',
                    'Desa/Kecamatan' => $service->landBook->desa_kecamatan ?? '-',
                    'Status' => $service->status,
                    'Nomor HP' => $service->nomor_hp,
                    'PNBP' => $service->PNBP,
                    'Keterangan' => $service->remarks,
                    'Tanggal Updated' => $service->created_at->format('Y-m-d H:i:s'),
                    'Status Alih Media' => $service->landBook->status_alih_media ?? '-',
                ];
            });
    }
}
