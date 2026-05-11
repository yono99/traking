<?php
use App\Http\Controllers\WaGatewayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
 
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/wa-session', function () {
    // Ambil session WA aktif dari DB
    // Pastikan model WaSession sudah ada, atau sesuaikan dengan tabel yang ada
    $session = \DB::table('wa_sessions')
        ->where('is_active', true)
        ->first();
 
    if (!$session) {
        return response()->json(['session_name' => null, 'message' => 'Tidak ada session aktif'], 404);
    }
 
    return response()->json(['session_name' => $session->session_name]);

});
// Route untuk menghitung jumlah data berdasarkan status
Route::get('services/count', [ServiceController::class, 'getServiceCounts']);

 
 
 
// Session aktif (dipakai GenggamBerkas, tidak perlu auth ketat)
Route::middleware(['auth:sanctum'])->get('/wa-session', [WaGatewayController::class, 'activeSession']);
 Route::post('/wa/send', [WaGatewayController::class, 'sendMessage']);