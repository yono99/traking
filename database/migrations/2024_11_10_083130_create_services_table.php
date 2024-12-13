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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('none'); // Nama layanan
            $table->foreignId('land_book_id')->constrained('land_books')->onDelete('cascade'); // Relasi ke LandBook
            $table->timestamps();
            $table->string('status')->default('FORWARD VERIFIKATOR'); // Default status awal
            $table->text('remarks')->nullable(); // Catatan tambahan
            $table->string('PNBP')->default('belum bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
