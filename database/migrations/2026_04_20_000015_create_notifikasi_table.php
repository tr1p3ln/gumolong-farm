<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: notifikasi
     * Modul: A-12 Notifikasi (NFR-02)
     */
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->bigIncrements('notifikasi_id');
            $table->unsignedBigInteger('user_id');
            $table->string('ear_tag_id', 20)->nullable();
            $table->enum('tipe', [
                'stok_menipis',
                'expired',
                'hpl',
                'vaksin',
                'adg_rendah'
            ]);
            $table->text('pesan');
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamp('tanggal_notifikasi');

            $table->foreign('user_id')
                  ->references('user_id')->on('user')
                  ->onDelete('cascade');

            $table->foreign('ear_tag_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
