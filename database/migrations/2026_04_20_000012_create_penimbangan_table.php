<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: penimbangan
     * Modul: A-06 Tracking Pertumbuhan (FR-4.1, FR-4.2)
     */
    public function up(): void
    {
        Schema::create('penimbangan', function (Blueprint $table) {
            $table->bigIncrements('timbangan_id');
            $table->string('ear_tag_id', 20);
            $table->date('tanggal_timbang');
            $table->decimal('berat_kg', 5, 2);
            $table->decimal('adg', 5, 3)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('ear_tag_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penimbangan');
    }
};
