<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ManagementAkunController extends Controller
{
    public function index()
    {
        return inertia('ManagementAkun'); // Merender halaman ManagementAkun.vue
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'unit' => 'required|string',
            'role' => 'required|string|in:admin,umum',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Pengguna dengan email ini tidak ditemukan.']);
        }

        $user->unit = $request->unit;
        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', 'Akun berhasil diperbarui!');
    }

}
