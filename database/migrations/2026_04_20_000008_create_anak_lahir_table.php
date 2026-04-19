<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: anak_lahir
     * Modul: A-09 Reproduksi (FR-7.2)
     * Catatan: ear_tag_id nullable - diisi setelah anak didaftarkan ke domba
     */
    public function up(): void
    {
        Schema::create('anak_lahir', function (Blueprint $table) {
            $table->bigIncrements('anak_id');
            $table->unsignedBigInteger('lahir_id');
            $table->string('ear_tag_id', 20)->nullable();
            $table->enum('jenis_kelamin', ['jantan', 'betina']);
            $table->decimal('bobot_lahir', 5, 2)->nullable();
            $table->enum('kondisi', ['hidup', 'mati'])->default('hidup');
            $table->timestamps();

            $table->foreign('lahir_id')
                  ->references('lahir_id')->on('kelahiran')
                  ->onDelete('cascade');

            $table->foreign('ear_tag_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anak_lahir');
    }
};
