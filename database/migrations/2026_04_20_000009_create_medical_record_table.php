<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: medical_record
     * Modul: A-07 Kesehatan Ternak (FR-5.1)
     */
    public function up(): void
    {
        Schema::create('medical_record', function (Blueprint $table) {
            $table->bigIncrements('rekam_id');
            $table->string('ear_tag_id', 20);
            $table->date('tanggal_sakit');
            $table->text('gejala');
            $table->text('diagnosa')->nullable();
            $table->date('tanggal_sembuh')->nullable();
            $table->enum('status', ['sakit', 'sembuh', 'mati', 'dalam_perawatan'])
                  ->default('sakit');
            $table->timestamps();

            $table->foreign('ear_tag_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_record');
    }
};
