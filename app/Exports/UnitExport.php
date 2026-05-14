<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UnitExport
{
    protected $startDate;
    protected $endDate;

    const STATUS_COLORS = [
        'SELESAI DISERAHKAN'          => ['059669', 'D1FAE5'],
        'PROSES LOKET'                => ['4F46E5', 'EEF2FF'],
        'PROSES BUKU TANAH'           => ['4F46E5', 'EEF2FF'],
        'PROSES PENGUKURAN'           => ['4F46E5', 'EEF2FF'],
        'PROSES VALIDASI BIDANG'      => ['4F46E5', 'EEF2FF'],
        'PROSES VALIDASI BUKU TANAH'  => ['4F46E5', 'EEF2FF'],
        'PROSES LOKET PENYERAHAN'     => ['4F46E5', 'EEF2FF'],
        'FORWARD LOKET'               => ['D97706', 'FEF9C3'],
        'FORWARD BUKU TANAH'          => ['D97706', 'FEF9C3'],
        'FORWARD PENGUKURAN'          => ['D97706', 'FEF9C3'],
        'FORWARD VALIDASI BIDANG'     => ['D97706', 'FEF9C3'],
        'FORWARD VALIDASI BUKU TANAH' => ['D97706', 'FEF9C3'],
        'FORWARD LOKET PENYERAHAN'    => ['D97706', 'FEF9C3'],
    ];

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function getData()
    {
        $userId = Auth::id();

        return \App\Models\Activity::with(['service.landBook'])
            ->where('user_id', $userId)
            ->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate   . ' 23:59:59',
            ])
            ->orderBy('created_at')
            ->get();
    }

    public function download(): StreamedResponse
    {
        $user     = Auth::user();
        $data     = $this->getData();
        $total    = $data->count();
        $exported = now()->format('d/m/Y H:i');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $ws = $spreadsheet->getActiveSheet()->setTitle('Aktivitas Saya');
        $ws->setShowGridLines(false);
        $ws->freezePane('A5');

        // Lebar kolom
        $widths = ['A'=>4,'B'=>18,'C'=>14,'D'=>16,'E'=>24,'F'=>28,'G'=>16,'H'=>16,'I'=>32];
        foreach ($widths as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        // ── Judul ────────────────────────────────────────────
        $ws->mergeCells('A1:I1');
        $this->cell($ws, 'A1', 'LAPORAN AKTIVITAS UNIT — SISTEM SMART',
            '059669', 'FFFFFF', 13, true, 'center', 34);

        $ws->mergeCells('A2:I2');
        $this->cell($ws, 'A2',
            "Periode: {$this->startDate}  s/d  {$this->endDate}   |   Petugas: {$user->name}   |   Diekspor: {$exported}",
            'D1FAE5', '065F46', 10, false, 'center', 18, true);

        $ws->getRowDimension(3)->setRowHeight(6);

        // ── Header ───────────────────────────────────────────
        $headers = [
            'A' => 'No',              'B' => 'Tanggal Update',
            'C' => 'Jenis Hak',       'D' => 'Nomor Hak',
            'E' => 'Desa/Kecamatan',  'F' => 'Status',
            'G' => 'PNBP',            'H' => 'Nomor HP',
            'I' => 'Keterangan',
        ];
        foreach ($headers as $col => $label) {
            $this->cell($ws, "{$col}4", $label, '065F46', 'FFFFFF', 10, true, 'center', 22);
        }

        // ── Data ─────────────────────────────────────────────
        foreach ($data as $i => $activity) {
            $row    = $i + 5;
            $bg     = $i % 2 === 0 ? 'FFFFFF' : 'F0FDF4';
            $status = $activity->service?->status ?? $activity->status ?? '-';
            [$sfg, $sbg] = self::STATUS_COLORS[$status] ?? ['334155', $bg];

            $this->cell($ws, "A{$row}", $i + 1,                                                        $bg,  '334155', 10, false, 'center');
            $this->cell($ws, "B{$row}", $activity->created_at?->format('d/m/Y H:i'),                   $bg,  '334155', 10, false, 'center');
            $this->cell($ws, "C{$row}", $activity->service?->landBook?->jenis_hak ?? '-',              $bg,  '334155');
            $this->cell($ws, "D{$row}", $activity->service?->landBook?->nomer_hak ?? '-',              $bg,  '334155');
            $this->cell($ws, "E{$row}", $activity->service?->landBook?->desa_kecamatan ?? '-',         $bg,  '334155', 10, false, 'left', 18, false, true);
            $this->cell($ws, "F{$row}", $status,                                                        $sbg, $sfg,    9,  true,  'center');
            $this->cell($ws, "G{$row}", $activity->service?->PNBP ?? '-',                              $bg,  '334155');
            $this->cell($ws, "H{$row}", $activity->service?->nomor_hp ?? '-',                          $bg,  '334155');
            $this->cell($ws, "I{$row}", $activity->remarks ?? '-',                                      $bg,  '334155', 10, false, 'left', 18, false, true);

            $ws->getRowDimension($row)->setRowHeight(18);
        }

        // ── Total ─────────────────────────────────────────────
        $tr = $total + 5;
        $ws->mergeCells("A{$tr}:E{$tr}");
        $this->cell($ws, "A{$tr}", 'TOTAL AKTIVITAS', '065F46', 'FFFFFF', 10, true, 'right',  20);
        $this->cell($ws, "F{$tr}", $total,             '065F46', 'FFFFFF', 10, true, 'center', 20);
        foreach (['G','H','I'] as $c) {
            $this->cell($ws, "{$c}{$tr}", '', '065F46', 'FFFFFF');
        }

        $filename = "aktivitas_unit_{$this->startDate}_{$this->endDate}.xlsx";

        return new StreamedResponse(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function cell($ws, string $coord, $value,
        string $bg='FFFFFF', string $fg='334155',
        int $sz=10, bool $bold=false, string $h='left',
        int $rowH=0, bool $italic=false, bool $wrap=false): void
    {
        $c = $ws->getCell($coord);
        $c->setValue($value);
        $c->getStyle()->getFont()->setName('Arial')->setSize($sz)->setBold($bold)->setItalic($italic)->getColor()->setRGB($fg);
        $c->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bg);
        $c->getStyle()->getAlignment()->setHorizontal($h)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText($wrap);
        $c->getStyle()->getBorders()->applyFromArray(['allBorders' => [
            'borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0'],
        ]]);
        if ($rowH > 0) {
            $row = (int) preg_replace('/\D/', '', $coord);
            $ws->getRowDimension($row)->setRowHeight($rowH);
        }
    }
}