<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: user
     * Modul: User Management (FR-1.2)
     */
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', [
                'super_admin',
                'admin',
                'kepala_kandang',
                'pengurus_kandang'
            ])->default('pengurus_kandang');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('nomor_hp', 20)->nullable();
            $table->string('foto_profile')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
