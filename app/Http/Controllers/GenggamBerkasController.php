<?php
namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

use App\Models\LandBook;

use App\Models\Service;
use Illuminate\Http\Request;

class GenggamBerkasController extends Controller
{
    
    public function index()
    {
         $landBooks = LandBook::all();// Mengambil semua data dari tabel `land_books`
         return inertia('GenggamBerkas', ['landBooks' => $landBooks]);
       // return inertia('GenggamBerkas');  Merender halaman GenggamBerkas.vue
    }
    public function create()
    {
        return inertia('GenggamBerkas'); // Pastikan Anda merender halaman Vue yang sesuai
    }

    public function store(Request $request)
    {
        $name = $request->input('name', 'null'); // Gunakan nilai default
        $PNBP = $request->input('PNBP', 'belum bayar'); // Gunakan nilai default
        // Validasi input
        $request->validate([
            'nomer_hak' => 'required|string|max:5',
            'desa_kecamatan' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:12',
            'jenis_hak' => 'required|string|in:HGB,HM,HMRS,HP,HW',
            'name' => 'nullable|string|max:255', // Validasi untuk kolom name
        ]);

        // Tetapkan nilai default untuk `name` jika kosong
       

        // Simpan data ke tabel `land_books`
        $landBook = LandBook::create([
            'nomer_hak' => $request->nomer_hak,
            'desa_kecamatan' => $request->desa_kecamatan,
            'jenis_hak' => $request->jenis_hak,
        ]);

        // Simpan data ke tabel `services`
        $service = Service::create([
            'land_book_id' => $landBook->id,
            'nomor_hp' => $request->nomor_hp,
            'name' => $name, // Masukkan default value untuk name
            'PNBP' =>  $PNBP,
        ]);
        
        // Simpan data aktivitas ke tabel `activities`
        Activity::create([
            'service_id' => $service->id,
            'user_id' => Auth::id(),
            'status' => 'FORWARD VERIFIKATOR',
            'remarks' => 'NONE',
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('genggam-berkas.create')->with('success', 'Data berhasil disimpan.');
    }
}
