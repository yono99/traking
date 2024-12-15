<?php

namespace App\Http\Controllers;

use App\Models\LandBook;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandBookController extends Controller
{
    public function update(Request $request, LandBook $landBook)
    {
        $validated = $request->validate([
            'nomer_hak' => 'required|string|max:255',
            'jenis_hak' => 'required|string|max:255',
            'desa_kecamatan' => 'required|string|max:255',
            'status_alih_media' => 'required|string|max:255',
        ]);

        $landBook->update($validated);

        return back()->with('success', 'Land book updated successfully');
    }
}