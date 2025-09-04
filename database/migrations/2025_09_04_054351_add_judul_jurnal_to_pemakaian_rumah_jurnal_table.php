<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pemakaian_rumah_jurnal', function (Blueprint $table) {
            // Tambah kolom judul_jurnal bersifat opsional
            $table->string('judul_jurnal', 300)->nullable()->after('mahasiswa_id');
        });
    }

    public function down(): void
    {
        Schema::table('pemakaian_rumah_jurnal', function (Blueprint $table) {
            $table->dropColumn('judul_jurnal');
        });
    }
};
