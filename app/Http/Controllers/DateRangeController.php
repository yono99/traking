<?php

namespace App\Http\Controllers;

use App\Exports\ServicesExport;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class DateRangeController extends Controller
{
    public function index()
    {
        return Inertia::render('Components/DateRange');
    }
    public function getDateRangeData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $result = DB::table('activities')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('status', 'FORWARD VERIFIKATOR')
                ->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $exporter = new ServicesExport($request->start_date, $request->end_date);
            $data = $exporter->getData();

            return (new FastExcel($data))
                ->download('laporan-berkas-' . $request->start_date . '-sampai-' . $request->end_date . '.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh data: ' . $e->getMessage());
        }
    }
}
