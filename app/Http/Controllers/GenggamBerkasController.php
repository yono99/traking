<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\LandBook;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GenggamBerkasController extends Controller
{
    public function index()
    {
        $landBooks = LandBook::all();
        return inertia('GenggamBerkas', ['landBooks' => $landBooks]);
    }

    public function create()
    {
        return inertia('GenggamBerkas');
    }

    public function store(Request $request)
    {
        $name = $request->input('name', 'null');
        $PNBP = $request->input('PNBP', 'belum bayar');

        // Validasi
        $request->validate([
            'nomer_hak'      => 'required|string|max:5',
            'desa_kecamatan' => 'required|string|max:255',
            'nomor_hp'       => 'required|string|max:12',
            'jenis_hak'      => 'required|string|in:HGB,HM,HMRS,HP,HW',
            'name'           => 'nullable|string|max:255',
            'file'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'nama_pemohon'   => 'nullable|string|max:255',
        ]);

        // Upload file ke land_books jika ada
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('land_books', 'public');
        }

        // Generate kode berkas unik
        $kode = 'BRK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        // Simpan ke tabel land_books
        $landBook = LandBook::create([
            'nomer_hak'      => $request->nomer_hak,
            'desa_kecamatan' => $request->desa_kecamatan,
            'jenis_hak'      => $request->jenis_hak,
            'file_path'      => $filePath,
        ]);

        // Simpan ke tabel services
        $service = Service::create([
            'land_book_id' => $landBook->id,
            'nomor_hp'     => $request->nomor_hp,
            'name'         => $name,
            'PNBP'         => $PNBP,
            'nama_pemohon' => $request->nama_pemohon,
            'kode_berkas'  => $kode,  // ← tambahan baru
        ]);

        // Simpan aktivitas
        Activity::create([
            'service_id' => $service->id,
            'user_id'    => Auth::id(),
            'status'     => 'FORWARD VERIFIKATOR',
            'remarks'    => 'NONE',
        ]);

        // Flash kode untuk ditampilkan di resume Vue
        return redirect()->route('genggam.berkas')->with('kode_berkas', $kode);
    }
}