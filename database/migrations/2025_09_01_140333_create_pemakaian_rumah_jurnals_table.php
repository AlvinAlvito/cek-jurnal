<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void {
        Schema::create('pemakaian_rumah_jurnal', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rumah_jurnal_id')->constrained('rumah_jurnal')->cascadeOnDelete();
            $table->foreignId('edisi_id')->constrained('edisi_rumah_jurnal')->cascadeOnDelete();

            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->restrictOnDelete();
            $table->foreignId('dosen_pembimbing_id')->constrained('dosen_pembimbing')->restrictOnDelete();

            $table->enum('status', ['pending','disetujui','dibatalkan'])->default('disetujui');
            $table->dateTime('digunakan_pada')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            // Aturan unik:
            $table->unique(['edisi_id', 'dosen_pembimbing_id']); // dosen tidak boleh >1 kali per edisi
            $table->unique(['edisi_id', 'mahasiswa_id']);        // mahasiswa tidak boleh daftar ganda per edisi

            // Index untuk cek ketersediaan cepat
            $table->index(['edisi_id', 'status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('pemakaian_rumah_jurnal');
    }
};