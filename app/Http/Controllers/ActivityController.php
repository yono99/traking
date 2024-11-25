<?php

namespace App\Http\Controllers;

use App\Models\LandBook;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Menampilkan daftar aktivitas.
     */
    public function index(Request $request)
    {
        // Tangkap parameter query
        $desaKecamatan = $request->query('desa_kecamatan');
        $jenisHak = $request->query('jenis_hak');
        $nomerHak = $request->query('nomer_hak');

        // Query data activities berdasarkan filter
        $activities = Activity::with(['service.landBook'])
            ->whereHas('service.landBook', function ($query) use ($desaKecamatan, $jenisHak, $nomerHak) {
                if ($desaKecamatan) {
                    $query->where('desa_kecamatan', $desaKecamatan);
                }
                if ($jenisHak) {
                    $query->where('jenis_hak', $jenisHak);
                }
                if ($nomerHak) {
                    $query->where('nomer_hak', $nomerHak);
                }
            })
            ->orderBy('created_at', 'asc') // Urutkan berdasarkan waktu
            ->get();

        // Format data untuk frontend
        $formattedActivities = $activities->map(function ($activity) {
            return [
                'status' => $activity->status,
                'remarks' => $activity->remarks,
                'created_at' => $activity->created_at,
                'nomer_hak' => $activity->service->landBook->nomer_hak ?? null,
                'jenis_hak' => $activity->service->landBook->jenis_hak ?? null,
                'desa_kecamatan' => $activity->service->landBook->desa_kecamatan ?? null,
            ];
        });

        // Kembalikan data ke frontend melalui Inertia
        return inertia('ActivityIndex', ['activities' => $formattedActivities]);
    }




    /**
     * Menyimpan data aktivitas baru.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        // Simpan data aktivitas
        Activity::create($request->all());

        // Redirect atau response sukses
        return back()->with('success', 'Aktivitas berhasil disimpan.');
    }


    public function fetch(Request $request)
    {
        $nomerHak = $request->input('nomer_hak');
        $jenisHak = $request->input('jenis_hak');
        $desaKecamatan = $request->input('desa_kecamatan');

        $activities = LandBook::query()
            ->when($nomerHak, function ($query) use ($nomerHak) {
                $query->where('nomer_hak', $nomerHak);
            })
            ->when($jenisHak, function ($query) use ($jenisHak) {
                $query->where('jenis_hak', $jenisHak);
            })
            ->when($desaKecamatan, function ($query) use ($desaKecamatan) {
                $query->where('desa_kecamatan', $desaKecamatan);
            })
            ->with(['services.activities.user'])
            ->get();

        // Format data menjadi array of objects
 $formattedActivities = $activities->flatMap(function ($landBook) {
    return $landBook->services->flatMap(function ($service) {
        return $service->activities->map(function ($activity) use ($service) {
            return [
                'nomer_hak' => $service->landBook->nomer_hak ?? null,
                'jenis_hak' => $service->landBook->jenis_hak ?? null,
                'desa_kecamatan' => $service->landBook->desa_kecamatan ?? null,
                'user_name' => $activity->user->name ?? 'Unknown',
                'activity_status' => $activity->status,
                'service_name' => $service->name,
                'service_contact' => $service->nomor_hp,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'), // Format waktu
            ];
        });
    });
});


        return response()->json($formattedActivities->flatten());
    }

}
