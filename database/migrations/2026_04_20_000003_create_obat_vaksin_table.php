<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: obat_vaksin
     */
    public function up(): void
    {
        Schema::create('obat_vaksin', function (Blueprint $table) {
            $table->bigIncrements('obat_id');
            $table->string('nama_obat');
            $table->enum('tipe', ['obat', 'vaksin', 'vitamin']);
            $table->enum('satuan', ['ml', 'dosis', 'tablet']);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(10);
            $table->decimal('harga_beli', 12, 2)->nullable();   
            $table->date('tanggal_expired')->nullable();
            $table->integer('interval_vaksinasi')->nullable();  
            $table->text('keterangan')->nullable();             
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat_vaksin');
    }
};