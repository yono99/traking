<?php

namespace App\Exports;

class ServicesExport
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
        return \App\Models\Service::with('landBook')
            ->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->get()
            ->map(function ($service) {
                return [
                    'Status' => $service->status,
                    'Nomor HP' => $service->nomor_hp,
                    'PNBP' => $service->PNBP,
                    'Remarks' => $service->remarks,
                    'Tanggal Dibuat' => $service->created_at->format('Y-m-d H:i:s'),
                    'Nomor Hak' => $service->landBook->nomer_hak ?? '-',
                    'Jenis Hak' => $service->landBook->jenis_hak ?? '-',
                    'Desa/Kecamatan' => $service->landBook->desa_kecamatan ?? '-',
                    'Status Alih Media' => $service->landBook->status_alih_media ?? '-',
                ];
            });
    }
}
