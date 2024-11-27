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
            'password' => Hash::make('EewqEt1@rasca'), // Ganti dengan password yang diinginkan
            'role' => 'admin', // Atur role sesuai kebutuhan
            'unit' => 'Developer', // Atur unit sesuai kebutuhan
        ]);
    }
}
