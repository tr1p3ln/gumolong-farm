<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: pakan_stok
     * Modul: A-04 Stok Pakan (FR-2.1, FR-2.2)
     */
    public function up(): void
    {
        Schema::create('pakan_stok', function (Blueprint $table) {
            $table->bigIncrements('pakan_id');
            $table->enum('jenis', ['rumput', 'konsentrat', 'silase', 'ampas_tahu']);
            $table->string('nama_pakan');
            $table->decimal('jumlah_stok', 10, 2)->default(0);
            $table->string('satuan', 20)->default('kg');
            $table->decimal('stok_minimum', 10, 2)->default(50);
            $table->date('tanggal_update');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pakan_stok');
    }
};
