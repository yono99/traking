<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Menampilkan daftar aktivitas.
     */
    public function index()
    {
        // Ambil semua data aktivitas
        $activities = Activity::with(['service', 'user'])->get();

        // Kembalikan data ke view (sesuaikan dengan kebutuhan Anda)
        return inertia('ActivityIndex', ['activities' => $activities]);
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
}
