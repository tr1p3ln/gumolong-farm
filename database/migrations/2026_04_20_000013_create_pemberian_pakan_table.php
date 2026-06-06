<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: pemberian_pakan
     * Modul: A-08 Pakan Individual & FCR (FR-6.1)
     * ISSUE #1 FIX: pakan_id FK ditambahkan
     * ISSUE #5 FIX: kolom fcr DIHAPUS (dihitung real-time via JOIN)
     */
    public function up(): void
    {
        Schema::create('pemberian_pakan', function (Blueprint $table) {
            $table->bigIncrements('pemberian_id');
            $table->unsignedBigInteger('pakan_id'); // ISSUE #1 FIX
            $table->string('ear_tag_id', 20);
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal_pemberian');
            $table->enum('sesi', ['pagi', 'sore']);
            $table->decimal('jumlah_gram', 8, 2);
            $table->timestamps();

            $table->foreign('pakan_id')
                  ->references('pakan_id')->on('pakan_stok')
                  ->onDelete('restrict');

            $table->foreign('ear_tag_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('user_id')->on('user')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemberian_pakan');
    }
};
