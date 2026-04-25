<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penimbangan', function (Blueprint $table) {
            $table->enum('status_validasi', ['pending', 'valid', 'ditolak'])->nullable()->after('catatan');
            $table->unsignedBigInteger('divalidasi_oleh')->nullable()->after('status_validasi');

            $table->foreign('divalidasi_oleh')
                  ->references('user_id')->on('user')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('penimbangan', function (Blueprint $table) {
            $table->dropForeign(['divalidasi_oleh']);
            $table->dropColumn(['status_validasi', 'divalidasi_oleh']);
        });
    }
};
