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
     *
     * Mengembalikan notifikasi berbasis Activity + Service.
     * Tidak butuh tabel tambahan — memanfaatkan data yang sudah ada.
     */
    public function index(Request $request)
    {
        $user     = Auth::user();
        $unit     = $user?->unit;
        $isAdmin  = $user?->role === 'admin';
        $limit    = 15;

        $notifications = collect();

        // ── 1. Berkas baru FORWARD ke unit pengguna (non-admin) ──────────
        if ($unit && ! $isAdmin) {
            $forwardStatus = 'FORWARD ' . strtoupper($unit);

            Service::with('land_book')
                ->where('status', $forwardStatus)
                ->latest('updated_at')
                ->limit($limit)
                ->get()
                ->each(function ($svc) use (&$notifications) {
                    $notifications->push([
                        'id'    => 'fwd-' . $svc->id,
                        'type'  => 'forward',
                        'title' => 'Berkas diteruskan ke unit Anda',
                        'body'  => ($svc->land_book?->nama_pemegang ?? 'Pemohon') .
                                   ' — ' . ($svc->land_book?->jenis_hak ?? '') .
                                   ' ' . ($svc->land_book?->nomer_hak ?? ''),
                        'time'  => $svc->updated_at
                                       ? Carbon::parse($svc->updated_at)->diffForHumans()
                                       : '-',
                        'read'  => false,
                    ]);
                });
        }

        // ── 2. Berkas selesai (admin) ────────────────────────────────────
        if ($isAdmin) {
            Service::with('land_book')
                ->where('status', 'SELESAI DISERAHKAN')
                ->latest('updated_at')
                ->limit(5)
                ->get()
                ->each(function ($svc) use (&$notifications) {
                    $notifications->push([
                        'id'    => 'done-' . $svc->id,
                        'type'  => 'selesai',
                        'title' => 'Berkas selesai diserahkan',
                        'body'  => ($svc->land_book?->nama_pemegang ?? 'Pemohon') .
                                   ' — ' . ($svc->land_book?->desa_kecamatan ?? ''),
                        'time'  => $svc->updated_at
                                       ? Carbon::parse($svc->updated_at)->diffForHumans()
                                       : '-',
                        'read'  => false,
                    ]);
                });
        }

        // ── 3. Aktivitas terbaru (dari Activity model) ───────────────────
        if (class_exists(Activity::class)) {
            $actQuery = Activity::latest()->limit(8);

            // Filter per unit kalau bukan admin
            if (! $isAdmin && $unit) {
                $actQuery->where(function ($q) use ($unit) {
                    $q->where('description', 'like', "%{$unit}%")
                      ->orWhere('unit', $unit); // sesuaikan kolom jika berbeda
                });
            }

            $actQuery->get()->each(function ($act) use (&$notifications) {
                $notifications->push([
                    'id'    => 'act-' . $act->id,
                    'type'  => 'baru',
                    'title' => $act->description ?? 'Aktivitas baru',
                    'body'  => $act->subject_type
                                   ? class_basename($act->subject_type) . ' #' . $act->subject_id
                                   : '',
                    'time'  => $act->created_at
                                   ? Carbon::parse($act->created_at)->diffForHumans()
                                   : '-',
                    'read'  => false,
                ]);
            });
        }

        return response()->json([
            'notifications' => $notifications
                ->sortByDesc(fn($n) => $n['time'])
                ->values()
                ->take($limit),
            'unread_count'  => $notifications->count(),
        ]);
    }
}