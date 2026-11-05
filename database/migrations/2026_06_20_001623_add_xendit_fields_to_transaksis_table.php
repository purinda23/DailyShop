<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {

            $table->foreignId('ekspedisi_id')
                  ->nullable()
                  ->constrained('ekspedisis')
                  ->nullOnDelete();

            $table->string('xendit_invoice_id')
                  ->nullable();

            $table->text('xendit_invoice_url')
                  ->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {

            $table->dropForeign(['ekspedisi_id']);

            $table->dropColumn([
                'ekspedisi_id',
                'xendit_invoice_id',
                'xendit_invoice_url'
            ]);

        });
    }
};