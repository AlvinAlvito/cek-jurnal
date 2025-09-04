<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambah kolom JSON sementara
        Schema::table('rumah_jurnal', function (Blueprint $table) {
            $table->json('edisi_json')->nullable()->after('tahun_akreditasi');
        });

        // Migrasi data lama (string -> array JSON)
        DB::table('rumah_jurnal')->select('id', 'edisi')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $r) {
                $val = $r->edisi ? [strtolower(trim($r->edisi))] : [];
                DB::table('rumah_jurnal')->where('id', $r->id)->update(['edisi_json' => json_encode($val)]);
            }
        });

        // Hapus kolom lama dan rename json -> edisi
        Schema::table('rumah_jurnal', function (Blueprint $table) {
            $table->dropColumn('edisi');
        });
        Schema::table('rumah_jurnal', function (Blueprint $table) {
            $table->renameColumn('edisi_json', 'edisi');
        });
    }

    public function down(): void
    {
        // Kembalikan ke string (ambil elemen pertama)
        Schema::table('rumah_jurnal', function (Blueprint $table) {
            $table->string('edisi_str', 20)->nullable()->after('tahun_akreditasi');
        });

        DB::table('rumah_jurnal')->select('id', 'edisi')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $r) {
                $arr = json_decode($r->edisi ?? '[]', true) ?: [];
                $first = $arr[0] ?? null;
                DB::table('rumah_jurnal')->where('id', $r->id)->update(['edisi_str' => $first]);
            }
        });

        Schema::table('rumah_jurnal', function (Blueprint $table) {
            $table->dropColumn('edisi');
        });
        Schema::table('rumah_jurnal', function (Blueprint $table) {
            $table->renameColumn('edisi_str', 'edisi');
        });
    }
};
