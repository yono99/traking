<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'yo',
            'email' => 'ree@genggamtanahku.my.id',
            'password' => '$2y$12$kcPXXbLT/4XFyaIAnl.Plu/ZJuaGqkt9McsQlKGeIJBPg/ZBmtJA6', // Ganti dengan password yang diinginkan
            'role' => 'admin', // Atur role sesuai kebutuhan
            'unit' => 'Developer', // Atur unit sesuai kebutuhan
        ]);
    }
}
