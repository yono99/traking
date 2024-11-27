<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Route untuk menghitung jumlah data berdasarkan status
Route::get('services/count', [ServiceController::class, 'getServiceCounts']);
