<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\Service;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanUnitController extends Controller
{
    /**
     * Tampilkan halaman laporan (Inertia)
     */
    public function index()
    {
        return Inertia::render('Laporan');
    }

    /**
     * API: ambil data aktivitas per tanggal & status
     * POST /api/get-laporan-unit
     */
    public function getLaporanUnit(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
    ]);

    try {
        // Data aktivitas harian per status (untuk LINE CHART - tetap dari Activity)
        $data = Activity::select(
                DB::raw('DATE(created_at) as date'),
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween(DB::raw('DATE(created_at)'), [
                $request->start_date,
                $request->end_date,
            ])
            ->groupBy(DB::raw('DATE(created_at)'), 'status')
            ->orderBy('date')
            ->get();

        // ── GANTI: Semua stats & volume dari Service (status CURRENT) ──

        $forwardStatuses = [
            'FORWARD LOKET', 'FORWARD BUKU TANAH', 'FORWARD PENGUKURAN',
            'FORWARD VALIDASI BIDANG', 'FORWARD VALIDASI BUKU TANAH',
            'FORWARD LOKET PENYERAHAN',
        ];
        $prosesStatuses = [
            'PROSES LOKET', 'PROSES BUKU TANAH', 'PROSES PENGUKURAN',
            'PROSES VALIDASI BIDANG', 'PROSES VALIDASI BUKU TANAH',
            'PROSES LOKET PENYERAHAN',
        ];

        $totalActivities = Service::count(); // total semua berkas

        $breakdown = Service::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $countProses  = array_sum(array_intersect_key(
            $breakdown->toArray(), array_flip($prosesStatuses)
        ));
        $countSelesai = $breakdown->get('SELESAI DISERAHKAN', 0);
        $countForward = array_sum(array_intersect_key(
            $breakdown->toArray(), array_flip($forwardStatuses)
        ));

        // Distribusi untuk DONUT CHART (dari Service)
        $distribution = Service::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        // Volume per unit untuk BAR CHART (dari Service)
        $unitMap = [
            'loket'            => ['PROSES LOKET', 'FORWARD LOKET'],
            'bukutanah'        => ['PROSES BUKU TANAH', 'PROSES VALIDASI BUKU TANAH',
                                   'FORWARD BUKU TANAH', 'FORWARD VALIDASI BUKU TANAH'],
            'pengukuran'       => ['PROSES PENGUKURAN', 'PROSES VALIDASI BIDANG',
                                   'FORWARD PENGUKURAN', 'FORWARD VALIDASI BIDANG'],
            'loket_penyerahan' => ['PROSES LOKET PENYERAHAN', 'FORWARD LOKET PENYERAHAN',
                                   'SELESAI DISERAHKAN'],
        ];

        $volumePerUnit = [];
        foreach ($unitMap as $unit => $statuses) {
            $count = array_sum(array_intersect_key(
                $breakdown->toArray(), array_flip($statuses)
            ));
            $volumePerUnit[] = ['unit' => $unit, 'count' => $count];
        }

        return response()->json([
            'success'          => true,
            'data'             => $data,           // untuk line chart
            'total_activities' => $totalActivities,
            'count_proses'     => $countProses,
            'count_selesai'    => $countSelesai,
            'count_forward'    => $countForward,
            'distribution'     => $distribution,
            'volume_per_unit'  => $volumePerUnit,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ], 500);
    }
}

    /**
     * API: ringkasan dashboard (stat cards)
     * GET /api/dashboard-stats
     */
    public function dashboardStats()
    {
        try {
            $total    = Service::count();
            $tunggu   = Service::whereIn('status', [
                'FORWARD LOKET','MENUNGGU PROSES'
            ])->count();
            $proses   = Service::whereIn('status', [
                'PROSES LOKET','PROSES BUKU TANAH','PROSES VALIDASI BUKU TANAH',
                'PROSES PENGUKURAN','PROSES VALIDASI BIDANG','PROSES LOKET PENYERAHAN',
            ])->count();
            $selesai  = Service::where('status', 'SELESAI DISERAHKAN')->count();

            // Distribusi untuk progress bar
            $distribution = Service::select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->get();

            return response()->json([
                'success'      => true,
                'total'        => $total,
                'tunggu'       => $tunggu,
                'proses'       => $proses,
                'selesai'      => $selesai,
                'distribution' => $distribution,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export Excel
     * GET /laporan-unit/export-excel?start_date=...&end_date=...
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = $request->start_date;
        $end   = $request->end_date;

        // Ambil data aktivitas
        $activities = Activity::with(['service', 'user'])
            ->whereBetween(DB::raw('DATE(activities.created_at)'), [$start, $end])
            ->orderBy('created_at')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Aktivitas');

        // ── Header ──
        $headers = ['No', 'Tanggal', 'Nomor Berkas', 'Nama Pemohon', 'Status Lama', 'Status Baru', 'Petugas', 'Keterangan'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $h);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
            $sheet->getStyle("{$col}1")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4F46E5');
            $sheet->getStyle("{$col}1")->getFont()->getColor()->setRGB('FFFFFF');
        }

        // ── Baris Data ──
        foreach ($activities as $idx => $act) {
            $row = $idx + 2;
            // Parse "Status berubah dari 'X' menjadi 'Y'" dari remarks
            preg_match("/dari '(.+?)' menjadi '(.+?)'/", $act->remarks ?? '', $m);
            $oldStatus = $m[1] ?? '-';
            $newStatus = $m[2] ?? ($act->status ?? '-');

            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValue("B{$row}", $act->created_at?->format('d/m/Y H:i'));
            $sheet->setCellValue("C{$row}", $act->service->Noberkas ?? '-');
            $sheet->setCellValue("D{$row}", $act->service->name ?? '-');
            $sheet->setCellValue("E{$row}", $oldStatus);
            $sheet->setCellValue("F{$row}", $newStatus);
            $sheet->setCellValue("G{$row}", $act->user->name ?? '-');
            $sheet->setCellValue("H{$row}", $act->remarks ?? '-');

            // Zebra striping
            if ($idx % 2 === 1) {
                $sheet->getStyle("A{$row}:H{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F5F5FF');
            }
        }

        // ── Sheet kedua: Ringkasan ──
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Ringkasan');
        $sheet2->setCellValue('A1', 'Ringkasan Laporan');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet2->setCellValue('A2', "Periode: {$start} s/d {$end}");
        $sheet2->setCellValue('A3', 'Total Aktivitas: ' . $activities->count());

        $byStatus = $activities->groupBy('status');
        $row = 5;
        $sheet2->setCellValue('A4', 'Status');
        $sheet2->setCellValue('B4', 'Jumlah');
        $sheet2->getStyle('A4:B4')->getFont()->setBold(true);
        foreach ($byStatus as $status => $items) {
            $sheet2->setCellValue("A{$row}", $status);
            $sheet2->setCellValue("B{$row}", $items->count());
            $row++;
        }

        // Auto-size kolom
        foreach ($spreadsheet->getAllSheets() as $ws) {
            foreach (range('A', 'H') as $col) {
                $ws->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $filename = "laporan_{$start}_{$end}.xlsx";

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}