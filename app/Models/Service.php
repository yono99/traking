<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'land_book_id', 'nomor_hp'];

    public function landBook()
    {
        return $this->belongsTo(LandBook::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
