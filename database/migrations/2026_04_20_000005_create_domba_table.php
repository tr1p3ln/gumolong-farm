<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: domba
     * FIX: Self-referencing FK (induk_id, ayah_id) harus ditambahkan
     * setelah tabel selesai dibuat — pisah Schema::create & Schema::table
     */
    public function up(): void
    {
        // STEP 1: Buat tabel domba dulu TANPA self-ref FK
        Schema::create('domba', function (Blueprint $table) {
            $table->string('ear_tag_id', 20)->primary();
            $table->string('nama')->nullable();
            $table->enum('jenis_kelamin', ['jantan', 'betina']);
            $table->string('ras');
            $table->date('tanggal_lahir')->nullable();
            $table->enum('kategori', ['cempe', 'dara', 'indukan', 'pejantan']);
            $table->enum('status', ['aktif', 'terjual', 'mati', 'karantina'])->default('aktif');
            $table->enum('asal', ['lahir_di_kandang', 'dari_luar'])->default('dari_luar');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('kandang_id');
            $table->string('induk_id', 20)->nullable();
            $table->string('ayah_id', 20)->nullable();
            $table->timestamps();

            // FK ke kandang (tabel lain — aman di sini)
            $table->foreign('kandang_id')
                  ->references('kandang_id')->on('kandang')
                  ->onDelete('restrict');
        });

        // STEP 2: Tambahkan self-ref FK setelah tabel sudah ada
        Schema::table('domba', function (Blueprint $table) {
            $table->foreign('induk_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('set null');

            $table->foreign('ayah_id')
                  ->references('ear_tag_id')->on('domba')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('domba', function (Blueprint $table) {
            $table->dropForeign(['induk_id']);
            $table->dropForeign(['ayah_id']);
        });

        Schema::dropIfExists('domba');
    }
};
