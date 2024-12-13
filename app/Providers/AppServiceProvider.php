<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Inertia::share([
            'auth' => [
                'user' => Auth::check() && Auth::user()
                    ? [
                        'id' => Auth::user()->id,
                        'name' => Auth::user()->name,
                        'role' => Auth::user()->role,
                    ]
                    : null,
            ],
        ]);
    }
}
