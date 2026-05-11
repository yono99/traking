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
              Schema::table('services', function (Blueprint $table) {
            $table->string('kode_berkas', 20)->nullable()->unique()->after('id');
            $table->string('file_path')->nullable()->after('nama_pemohon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['kode_berkas', 'file_path']);
        });
    }
};
