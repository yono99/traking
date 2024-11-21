<?php
// app/Http/Controllers/ServiceController.php
// app/Http/Controllers/ServiceController.php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update([
            'status' => $request->status
        ]);

        return redirect()->route('inventory.index');
    }
}
