<?php

use App\Http\Controllers\ManagementAkunController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\GenggamBerkasController;
use App\Http\Middleware\LoketMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TanyaGenggamController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\CheckUnit;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', CheckRole::class . ':admin'])->group(function () {
    Route::get('/management-akun', [ManagementAkunController::class, 'index'])->name('management.akun');
    Route::post('/management-akun/update', [ManagementAkunController::class, 'update'])->name('management.akun.update');
});

Route::middleware(['auth', LoketMiddleware::class . ':loket' ])->group(function () {
    Route::get('/genggam-berkas', [GenggamBerkasController::class, 'index'])->name('genggam.berkas');
    Route::get('/genggam-berkas/create', [GenggamBerkasController::class, 'create'])->name('genggam-berkas.create');
    Route::post('/genggam-berkas', [GenggamBerkasController::class, 'store'])->name('genggam-berkas.store');

});

// Rute untuk TanyaGenggam
Route::middleware(['auth', CheckUnit::class . ':verifikator,pengukuran,bukutanah,sps,QC,pengesahan,paraf,TTE_PRODUK_LAYANAN'])->group(function () {

    // Rute untuk menampilkan halaman TanyaGenggam
    Route::get('/tanya-genggam', [TanyaGenggamController::class, 'index'])->name('tanya-genggam.index');

    // Rute untuk menyimpan data dengan metode POST
    Route::post('/tanya-genggam', [TanyaGenggamController::class, 'store'])->name('tanya-genggam.store');

});

// Rute untuk pencarian berdasarkan nomer_hak
Route::get('/search', [TanyaGenggamController::class, 'search']);


// Rute untuk update status
Route::post('/update-status', [SearchController::class, 'updateStatus'])->middleware('auth');
