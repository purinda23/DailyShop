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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // $table->foreignId('ekspedisi_id')->constrained('ekspedisis', 'id')->onDelete('cascade');
            // $table->foreignId('ekspedisi_id')
            //         ->constrained('ekspedisis')
            //         ->onDelete('cascade');
            $table->string('kode_transaksi')->unique();
            $table->decimal('total_harga', 12, 2);
            $table->text('alamat_pengiriman');
            $table->string('metode_pembayaran');
            $table->enum('status', ['proses', 'dikirim', 'selesai', 'batal']);
            
            // Satukan kolom bukti_pembayaran langsung di sini (nullable karena dipakai nanti)
            $table->string('bukti_pembayaran')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};