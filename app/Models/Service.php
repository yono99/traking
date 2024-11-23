<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';

    protected $fillable = [
        'land_book_id',
        'nomor_hp',
    ];

    // Model Service.php
    public function activities()
    {
        return $this->hasMany(Activity::class, 'service_id');
    }

    public function landBook()
    {
        return $this->belongsTo(LandBook::class, 'land_book_id');
    }

}
