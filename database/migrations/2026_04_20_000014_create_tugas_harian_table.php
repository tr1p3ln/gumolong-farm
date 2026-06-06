<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Tabel: tugas_harian
//      * Modul: A-11 Daily Task Monitor (FR-8.1, FR-8.2)
//      */
//     public function up(): void
//     {
//         Schema::create('tugas_harian', function (Blueprint $table) {
//             $table->bigIncrements('tugas_id');
//             $table->date('tanggal_tugas');
//             $table->enum('jenis', ['feeding', 'sanitasi', 'observasi_visual']);
//             $table->string('ear_tag_id', 20)->nullable();
//             $table->unsignedBigInteger('user_id');
//             $table->boolean('selesai')->default(false);
//             $table->timestamp('waktu_selesai')->nullable();
//             $table->text('catatan')->nullable();
//             $table->timestamps();

//             $table->foreign('ear_tag_id')
//                   ->references('ear_tag_id')->on('domba')
//                   ->onDelete('set null');

//             $table->foreign('user_id')
//                   ->references('user_id')->on('user')
//                   ->onDelete('restrict');
//         });
//     }

//     public function down(): void
//     {
//         Schema::dropIfExists('tugas_harian');
//     }
// };

return new class extends Migration
{
    /**
     * Tabel: tugas_harian
     * Modul: A-11 Daily Task Monitor
     */
    public function up(): void
    {
        Schema::create('tugas_harian', function (Blueprint $table) {
            $table->id(); // atau bigIncrements('tugas_id') jika model Anda mengaturnya secara khusus
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            
            // Kolom Relasi
            $table->unsignedBigInteger('kandang_id');
            $table->unsignedBigInteger('user_id')->nullable();
            
            // Kolom Operasional
            $table->date('tanggal'); // Sesuai dengan controller
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi']);
            $table->enum('status', ['belum', 'dalam_proses', 'selesai', 'dilewati'])->default('belum');
            
            // Waktu & Catatan
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->text('catatan_penyelesaian')->nullable();
            
            $table->timestamps();

            // Foreign Keys (Sesuaikan dengan nama tabel utama Anda)
            // $table->foreign('kandang_id')->references('kandang_id')->on('kandang')->onDelete('cascade');
            // $table->foreign('user_id')->references('user_id')->on('user')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_harian');
    }
};