<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data pengguna dengan pagination (10 data per halaman)
        $users = User::select('id', 'name', 'email', 'role', 'unit')
            ->paginate(10);

        // Kembalikan data dalam format JSON
        return response()->json($users);
    }
}
