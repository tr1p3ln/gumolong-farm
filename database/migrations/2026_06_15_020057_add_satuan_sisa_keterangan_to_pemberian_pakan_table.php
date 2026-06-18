<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pemberian_pakan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemberian_pakan', 'satuan')) {
                $table->string('satuan')->nullable()->after('jumlah_gram');
            }
            if (!Schema::hasColumn('pemberian_pakan', 'sisa_stok')) {
                $table->decimal('sisa_stok', 10, 2)->nullable()->after('satuan');
            }
            if (!Schema::hasColumn('pemberian_pakan', 'keterangan')) {
                $table->string('keterangan')->nullable()->after('sisa_stok');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pemberian_pakan', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'sisa_stok', 'keterangan']);
        });
    }
};
