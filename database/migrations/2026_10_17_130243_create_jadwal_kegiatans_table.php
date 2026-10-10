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
        Schema::create('jadwal_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_kegiatan');
            $table->string('slug')->unique();
            $table->string('penceramah_atau_penanggung_jawab')->nullable();
            $table->string('lokasi')->default('Ruang Utama Masjid');
            $table->date('tanggal_mulai');
            $table->time('waktu_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->enum('status', ['akan_datang', 'berlangsung', 'selesai', 'dibatalkan'])->default('akan_datang');
            $table->text('deskripsi')->nullable();
            $table->string('poster')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_kegiatans');
    }
};
