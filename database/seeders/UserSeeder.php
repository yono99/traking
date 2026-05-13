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
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'admin',
            'unit' => 'Developer',
        ]);
        User::create([
            'name' => 'regi',
            'email' => 'loket@1.1',
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'umum',
            'unit' => 'loket',
        ]);
        User::create([
            'name' => 'wahyu',
            'email' => 'verifikaktor@1.1',
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'umum',
            'unit' => 'verifikaktor',
        ]);
        User::create([
            'name' => 'erik',
            'email' => 'sps@1.1',
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'umum',
            'unit' => 'sps',
        ]);
        User::create([
            'name' => 'juun',
            'email' => 'bukutanah@1.1',
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'umum',
            'unit' => 'bukutanah',
        ]);
        User::create([
            'name' => 'buna',
            'email' => 'pengukuran@1.1',
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'umum',
            'unit' => 'pengukuran',
        ]);
        User::create([
            'name' => 'budi',
            'email' => 'bensus@1.1',
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'umum',
            'unit' => 'bensus',
        ]);
        User::create([
            'name' => 'kiana',
            'email' => 'pelaksana@1.1',
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'umum',
            'unit' => 'pelaksana',
        ]);
        User::create([
            'name' => 'budiq',
            'email' => 'paraf@1.1',
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'umum',
            'unit' => 'paraf',
        ]);
        User::create([
            'name' => 'karman',
            'email' => 'TTE@1.1',
            'password' => '$2y$12$8cxdgpjbSPnpFvjD/0mS6.NUE43UpxmX.Aud78ON.nYVq10I8Cpdu',
            'role' => 'umum',
            'unit' => 'TTE_PRODUK_LAYANAN',
        ]);
    }
}
