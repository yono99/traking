<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandBook; // Assuming LandBook is the model for the 'land_books' table

class Hitung_berkas_alihmedia_rutinController extends Controller
{
    public function hitungBerkasAlihmediaRutin()
    {
        // Count jumlah_status_alihmedia
        $jumlahStatusAlihmedia = LandBook::whereIn('status_alih_media', ['Alih Media', 'FORWARD PELAKSANA CETAK SERTEL'])->count();

        // Count jumlah_status_rutin
        $jumlahStatusRutin = LandBook::whereIn('status_alih_media', ['Rutin', 'FORWARD PELAKSANA CETAK SERTEL'])->count();

        // Return response
        return response()->json([
            'jumlah_status_alihmedia' => $jumlahStatusAlihmedia,
            'jumlah_status_rutin' => $jumlahStatusRutin,
        ]);
    }
}
