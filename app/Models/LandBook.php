<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandBook extends Model
{
    use HasFactory;
    protected $table = 'land_books';

    protected $fillable = [
        'nomer_hak',
        'desa_kecamatan',
        'jenis_hak',
        'status_alih_media',
    ];

    // Model LandBook.php
    public function services()
    {
        return $this->hasMany(Service::class, 'land_book_id');

    }

 
}
