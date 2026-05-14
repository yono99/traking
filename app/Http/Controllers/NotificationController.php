<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications
     */
    public function index(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user?->role === 'admin';
        $unit    = $user?->unit;
        $limit   = 15;

        $notifications = collect();

        // ── 1. Permohonan baru (Activity dengan status awal / PROSES LOKET) ──
        $newQuery = Activity::with(['service', 'service.landBook'])
            ->whereHas('service')
            ->latest()
            ->limit($limit);

        $newQuery->get()->each(function ($act) use (&$notifications) {
            $service    = $act->service;
            $landBook   = $service?->landBook;

            $kode       = $service?->kode_berkas ?? 'N/A';
            $pemohon    = $service?->nama_pemohon
                        ?? $landBook?->nama_pemegang
                        ?? 'Tidak diketahui';
            $status     = $act->status ?? $service?->status ?? '-';

            // Tentukan tipe notifikasi berdasarkan status activity
            if (str_contains(strtoupper($status), 'SELESAI')) {
                $type  = 'selesai';
                $title = 'Berkas selesai diserahkan';
            } elseif (str_contains(strtoupper($status), 'FORWARD')) {
                $type  = 'forward';
                $title = 'Berkas diteruskan';
            } else {
                $type  = 'baru';
                $title = 'Permohonan baru masuk';
            }

            $notifications->push([
                'id'    => 'act-' . $act->id,
                'type'  => $type,
                'title' => $title,
                'body'  => 'Kode: ' . $kode . ' — ' . $pemohon,
                'time'  => $act->created_at
                               ? Carbon::parse($act->created_at)->diffForHumans()
                               : '-',
                'read'  => false,
            ]);
        });

        // ── 2. Berkas forward ke unit pengguna (non-admin) ──
        if ($unit && !$isAdmin) {
            $forwardStatus = 'FORWARD ' . strtoupper($unit);

            Service::with('landBook')
                ->where('status', $forwardStatus)
                ->latest('updated_at')
                ->limit(5)
                ->get()
                ->each(function ($svc) use (&$notifications) {
                    $kode    = $svc->kode_berkas ?? 'N/A';
                    $pemohon = $svc->nama_pemohon
                             ?? $svc->landBook?->nama_pemegang
                             ?? 'Tidak diketahui';

                    $notifications->push([
                        'id'    => 'fwd-' . $svc->id,
                        'type'  => 'forward',
                        'title' => 'Berkas diteruskan ke unit Anda',
                        'body'  => 'Kode: ' . $kode . ' — ' . $pemohon,
                        'time'  => $svc->updated_at
                                       ? Carbon::parse($svc->updated_at)->diffForHumans()
                                       : '-',
                        'read'  => false,
                    ]);
                });
        }

        // ── 3. Berkas selesai (admin only) ──
        if ($isAdmin) {
            Service::with('landBook')
                ->where('status', 'SELESAI DISERAHKAN')
                ->latest('updated_at')
                ->limit(5)
                ->get()
                ->each(function ($svc) use (&$notifications) {
                    $kode    = $svc->kode_berkas ?? 'N/A';
                    $pemohon = $svc->nama_pemohon
                             ?? $svc->landBook?->nama_pemegang
                             ?? 'Tidak diketahui';

                    $notifications->push([
                        'id'    => 'done-' . $svc->id,
                        'type'  => 'selesai',
                        'title' => 'Berkas selesai diserahkan',
                        'body'  => 'Kode: ' . $kode . ' — ' . $pemohon,
                        'time'  => $svc->updated_at
                                       ? Carbon::parse($svc->updated_at)->diffForHumans()
                                       : '-',
                        'read'  => false,
                    ]);
                });
        }

        // Deduplikasi berdasarkan ID, urutkan terbaru
        $result = $notifications
            ->unique('id')
            ->sortByDesc('time')
            ->values()
            ->take($limit);

        return response()->json([
            'notifications' => $result,
            'unread_count'  => $result->count(),
        ]);
    }
}