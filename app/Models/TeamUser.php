<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Teams;

class TeamUser extends Model
{
    protected $table = 'team_user';

    // Jika ada kolom tambahan di tabel pivot, tambahkan di sini
    protected $fillable = ['team_id', 'user_id']; // contoh 'role' bisa jadi admin/member

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teams()
    {
        return $this->belongsTo(Teams::class, 'team_id');
    }
}
