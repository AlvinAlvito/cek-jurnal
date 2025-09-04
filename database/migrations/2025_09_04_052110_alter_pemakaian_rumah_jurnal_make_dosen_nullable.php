<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pemakaian_rumah_jurnal', function (Blueprint $table) {
            // 1) Jika sebelumnya ada FK, putuskan dulu
            // nama constraint biasanya: pemakaian_rumah_jurnal_dosen_pembimbing_id_foreign
            if (Schema::hasColumn('pemakaian_rumah_jurnal', 'dosen_pembimbing_id')) {
                try {
                    $table->dropForeign(['dosen_pembimbing_id']);
                } catch (\Throwable $e) {
                    // ignore kalau memang belum ada FK
                }
            }

            // 2) Ubah kolom jadi nullable
            $table->unsignedBigInteger('dosen_pembimbing_id')->nullable()->change();

            // 3) Tambahkan kembali FK dengan ON DELETE SET NULL (opsional tapi direkomendasikan)
            $table->foreign('dosen_pembimbing_id')
                  ->references('id')->on('dosen_pembimbing')
                  ->nullOnDelete(); // set null jika dosen dihapus
        });
    }

    public function down(): void
    {
        Schema::table('pemakaian_rumah_jurnal', function (Blueprint $table) {
            try {
                $table->dropForeign(['dosen_pembimbing_id']);
            } catch (\Throwable $e) {}

            // Kembalikan jadi NOT NULL (kalau mau)
            $table->unsignedBigInteger('dosen_pembimbing_id')->nullable(false)->change();

            // Pasang lagi FK default (tanpa set null)
            $table->foreign('dosen_pembimbing_id')
                  ->references('id')->on('dosen_pembimbing');
        });
    }
};
