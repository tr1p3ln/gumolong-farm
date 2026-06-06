<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: perkawinan
     * Modul: A-09 Reproduksi (FR-7.1, FR-7.2)
     * ISSUE #2 FIX:
     * - ENUM status diupdate ke 5 nilai
     * - Tambah 4 kolom konfirmasi kebuntingan
     */
    public function up(): void
    {
        Schema::create('perkawinan', function (Blueprint $table) {
            $table->bigIncrements('kawin_id');
            $table->string('pejantan_id', 20);
            $table->string('indukan_id', 20);
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal_perkawinan');
            $table->enum('metode', ['alami', 'inseminasi_buatan'])->default('alami');
            $table->date('estimasi_lahir')->nullable();
            $table->enum('status', [
                'menunggu_konfirmasi',
                'bunting',
                'tidak_bunting',
                'lahir',
                'gagal'
            ])->default('menunggu_konfirmasi');

            // ISSUE #2 FIX: 4 kolom konfirmasi kebuntingan
            $table->date('tgl_konfirmasi')->nullable();
            $table->enum('metode_konfirmasi', ['USG', 'observasi_fisik'])->nullable();
            $table->text('catatan_konfirmasi')->nullable();
            $table->unsignedBigInteger('dikonfirmasi_oleh')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('pejantan_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('restrict');

            $table->foreign('indukan_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('restrict');

            $table->foreign('user_id')
                  ->references('user_id')->on('user')
                  ->onDelete('restrict');

            $table->foreign('dikonfirmasi_oleh')
                  ->references('user_id')->on('user')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perkawinan');
    }
};
