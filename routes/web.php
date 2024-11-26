<?php

use App\Http\Controllers\ManagementAkunController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\GenggamBerkasController;
use App\Http\Middleware\LoketMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TanyaGenggamController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\CheckUnit;
use App\Http\Controllers\TeamsController;

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

    Route::get('/teams', [TeamsController::class, 'index'])->name('teams.index');
    Route::post('/teams', [TeamsController::class, 'store'])->name('teams.store');
    Route::post('/teams/{team}/add-member', [TeamsController::class, 'addMember'])->name('teams.addMember');
    Route::delete('/teams/{team}/remove-member/{user}', [TeamsController::class, 'removeMember'])->name('teams.removeMember');
    Route::post('/users/{user}/move-team', [TeamsController::class, 'moveMember'])->name('users.moveTeam');
    Route::delete('/teams/{team}', [TeamsController::class, 'destroy'])->name('teams.destroy');

});

Route::middleware(['auth', LoketMiddleware::class . ':loket' ])->group(function () {
    Route::get('/genggam-berkas', [GenggamBerkasController::class, 'index'])->name('genggam.berkas');
    Route::get('/genggam-berkas/create', [GenggamBerkasController::class, 'create'])->name('genggam-berkas.create');
    Route::post('/genggam-berkas', [GenggamBerkasController::class, 'store'])->name('genggam-berkas.store');

});

// Rute untuk TanyaGenggam dan Inventory
Route::middleware(['auth', CheckUnit::class . ':main'])->group(function () {

    // Rute untuk menampilkan halaman TanyaGenggam
    Route::get('/tanya-genggam', [TanyaGenggamController::class, 'index'])->name('tanya-genggam.index');

    // Rute untuk menyimpan data dengan metode POST
    Route::post('/tanya-genggam', [TanyaGenggamController::class, 'store'])->name('tanya-genggam.store');
    // Rute untuk pencarian berdasarkan nomer_hak
    Route::get('/search', [TanyaGenggamController::class, 'search']);
    // Rute untuk update status
    Route::post('/update-status', [SearchController::class, 'updateStatus']);
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

    route::post('/inventory/update-status/{serviceId}', [InventoryController::class, 'updateStatus'])->name('inventory.updateStatus');
});

// Tambahkan route ini di web.php
Route::get('/activities/fetch', [ActivityController::class, 'fetch']);
