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
        Schema::create('aktivitas_penggunas', function (Blueprint $table) {
            $table->id();

            // relasi ke user
            $table->foreignId('id_pengguna')->nullable()->constrained('users')->onDelete('cascade');

            // relasi ke produk
            $table->string('kode_produk')->nullable();

            // waktu aktivitas (untuk latest)
            $table->timestamp('waktu_aktivitas')->nullable();

            $table->string('aktivitas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_penggunas');
    }
};