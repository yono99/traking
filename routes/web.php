<?php

use App\Http\Controllers\ManagementAkunController;
// Route untuk halaman managemen akun
use App\Http\Middleware\CheckRole;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

