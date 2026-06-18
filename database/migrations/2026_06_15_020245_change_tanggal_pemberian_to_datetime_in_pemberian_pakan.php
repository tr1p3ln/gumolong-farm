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
            $table->dateTime('tanggal_pemberian')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pemberian_pakan', function (Blueprint $table) {
            $table->date('tanggal_pemberian')->change();
        });
    }
};
