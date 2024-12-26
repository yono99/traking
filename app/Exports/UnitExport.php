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
        return \App\Models\Activity::with('landBook', 'service')

            ->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->get()
            ->where('user_id', $currentUserId)
            ->map(function ($fetch) {
                return [
                    'Jenis Hak' => $fetch->service->landBook->jenis_hak ?? '-',
                    'Nomor Hak' => $fetch->service->landBook->nomer_hak ?? '-',
                    'Desa/Kecamatan' => $fetch->service->landBook->desa_kecamatan ?? '-',
                    'Status' => $fetch->service->status,
                    'Nomor HP' => $fetch->service->nomor_hp,
                    'PNBP' => $fetch->service->PNBP,
                    'Keterangan' => $fetch->remarks,
                    'Tanggal Updated' => $fetch->created_at->format('Y-m-d H:i:s'),
                    'Status Alih Media' => $fetch->service->landBook->status_alih_media ?? '-',
                ];
            });
    }
}
