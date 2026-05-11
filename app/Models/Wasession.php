<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaSession extends Model
{
    protected $fillable = ['session_name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}