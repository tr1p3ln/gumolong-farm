<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: kandang
     * Modul: Master Data Kandang
     */
    public function up(): void
    {
        Schema::create('kandang', function (Blueprint $table) {
            $table->bigIncrements('kandang_id');
            $table->string('nama_kandang');
            $table->enum('tipe', ['utama', 'isolasi', 'kawin', 'persalinan'])
                  ->default('utama');
            $table->integer('kapasitas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kandang');
    }
};
