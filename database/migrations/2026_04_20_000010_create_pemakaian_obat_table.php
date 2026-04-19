<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: pemakaian_obat
     * Modul: A-07 Kesehatan Ternak (FR-5.1)
     */
    public function up(): void
    {
        Schema::create('pemakaian_obat', function (Blueprint $table) {
            $table->bigIncrements('pakai_id');
            $table->unsignedBigInteger('rekam_id');
            $table->unsignedBigInteger('obat_id');
            $table->integer('jumlah');
            $table->date('tanggal_pakai');
            $table->timestamps();

            $table->foreign('rekam_id')
                  ->references('rekam_id')->on('medical_record')
                  ->onDelete('cascade');

            $table->foreign('obat_id')
                  ->references('obat_id')->on('obat_vaksin')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemakaian_obat');
    }
};
