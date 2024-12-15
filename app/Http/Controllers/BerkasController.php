<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class BerkasController extends Controller
{
    public function index()
    {
        // Mengambil semua data service dengan relasi land_book
        $services = Service::with('landBook')->get();

        return Inertia::render('Berkas', [
            'services' => $services,
            'user' => Auth::user(),
        ]);
    }
}
