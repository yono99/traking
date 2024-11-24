<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'status',
        'remarks',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // Model Activity.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
