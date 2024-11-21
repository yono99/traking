<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomer_hak',
        'desa_kecamatan',
        'jenis_hak',
        'status_alih_media',
    ];

    // Model LandBook.php
    public function service()
    {
        return $this->hasOne(Service::class, 'land_book_id');
    }
    public function landBook()
    {
        return $this->belongsTo(LandBook::class, 'land_book_id');
    }
}
