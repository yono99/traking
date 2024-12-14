<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('land_books', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_hak'); // Nomor hak
            $table->string('jenis_hak');
            $table->string('desa_kecamatan');
            $table->string('status_alih_media')->default('null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_books');
    }
};
