<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'land_book_id',
        'user_id',
        'status',
        'remarks',
        'create_at',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function landBook()
    {
        return $this->belongsTo(LandBook::class, 'land_book_id');
    }

    // Model Activity.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
