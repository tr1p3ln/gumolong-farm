<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas_harian', function (Blueprint $table) {
            $table->enum('tipe', ['rutin', 'kondisional'])->default('rutin')->after('tanggal');
            $table->unsignedBigInteger('assigned_by')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('tugas_harian', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'assigned_by']);
        });
    }
};
