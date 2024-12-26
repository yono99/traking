<?php

namespace App\Http\Controllers;

use App\Exports\ServicesExport;
use App\Exports\UnitExport;
use App\Models\Service;
use App\Models\Activity;
use App\Models\LandBook;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
 
use Rap2hpoutre\FastExcel\FastExcel;
class LaporanUnitController extends Controller
{
    public function getDateRangeData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
           
       
            $currentUserId = Auth::user()->id; // Cara yang lebih singkat untuk mendapatkan ID user
            
            // Query untuk menghitung jumlah aktivitas per tanggal dan status
            $result = DB::table('activities')
            ->select(
                DB::raw('DATE(created_at) as date'),
                'status',
                DB::raw('COUNT(*) as count')
            )
                ->where('user_id', $currentUserId)
                ->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ])
                ->groupBy(DB::raw('DATE(created_at)'), 'status') // Gunakan DB::raw untuk DATE
                ->orderBy('date')
                ->get();

            // Hitung total aktivitas
            $totalActivities = DB::table('activities')
            ->where('user_id', $currentUserId)
                ->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ])
                ->count();

            return response()->json([
                'success' => true,
                'data' => $result,
                'total_activities' => $totalActivities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        try {
            $exporter = new UnitExport($request->start_date, $request->end_date);
            $data = $exporter->getData();

            return (new FastExcel($data))
                ->download('laporan-berkas-' . $request->start_date . '-sampai-' . $request->end_date . '.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh data: ' . $e->getMessage());
        }
    }

    public function index()
    {
        return Inertia::render('LaporanUnit');
    }
}