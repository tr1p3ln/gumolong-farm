<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: kelahiran
     * Modul: A-09 Reproduksi (FR-7.2)
     */
    public function up(): void
    {
        Schema::create('kelahiran', function (Blueprint $table) {
            $table->bigIncrements('lahir_id');
            $table->unsignedBigInteger('kawin_id');
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal_kelahiran');
            $table->integer('jml_anak_hidup');
            $table->integer('jml_anak_mati')->default(0);
            $table->decimal('bobot_rata_rata', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('kawin_id')
                  ->references('kawin_id')->on('perkawinan')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('user_id')->on('user')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelahiran');
    }
};
