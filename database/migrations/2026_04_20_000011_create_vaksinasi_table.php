<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: vaksinasi
     * Modul: A-07 Kesehatan Ternak (FR-5.2)
     */
    public function up(): void
    {
        Schema::create('vaksinasi', function (Blueprint $table) {
            $table->bigIncrements('vaksin_id');
            $table->string('ear_tag_id', 20);
            $table->unsignedBigInteger('obat_id');
            $table->date('tanggal_vaksinasi');
            $table->date('tanggal_berikutnya')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('ear_tag_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('cascade');

            $table->foreign('obat_id')
                  ->references('obat_id')->on('obat_vaksin')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaksinasi');
    }
};
