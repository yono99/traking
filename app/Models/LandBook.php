<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandBook extends Model
{
    use HasFactory;

    protected $fillable = ['nomer_hak', 'jenis_hak', 'desa', 'kecamatan', 'status_alih_media'];

    public function service()
    {
        return $this->hasOne(Service::class);
    }
}
