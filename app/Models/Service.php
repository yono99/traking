<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';

    protected $fillable = [
        'name',
        'land_book_id',
        'status',
        'PNBP',
        'nomor_hp'
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
    // App\Models\Service.php
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
