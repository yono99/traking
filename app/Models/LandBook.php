<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandBook extends Model
{
    use HasFactory;
    protected $table = 'land_books';

    protected $fillable = [
        'service_id',
        'activities_id',
        'nomer_hak',
        'desa_kecamatan',
        'jenis_hak',
        'status_alih_media',
    ];

    // Model LandBook.php
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'activities_id');
    }
}
