<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
    function up(): void
    {
        Schema::table('pemakaian_obat', function (Blueprint $table) {
            $table->string('cara_pemberian', 255)->nullable()->after('tanggal_pakai');
            $table->text('catatan')->nullable()->after('cara_pemberian');
        });
    }

    public function down(): void
    {
        Schema::table('pemakaian_obat', function (Blueprint $table) {
            $table->dropColumn(['cara_pemberian', 'catatan']);
        });
    }
};
