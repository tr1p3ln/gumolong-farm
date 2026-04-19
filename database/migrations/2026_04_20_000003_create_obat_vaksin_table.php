<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: obat_vaksin
     * Modul: A-05 Obat & Vaksin (FR-3.1)
     */
    public function up(): void
    {
        Schema::create('obat_vaksin', function (Blueprint $table) {
            $table->bigIncrements('obat_id');
            $table->string('nama_obat');
            $table->enum('tipe', ['obat', 'vaksin']);
            $table->enum('satuan', ['ml', 'dosis', 'tablet']);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(10);
            $table->date('tanggal_expired')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat_vaksin');
    }
};
