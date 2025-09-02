<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void {
        Schema::create('pengaturan_jurnal', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('max_mahasiswa_per_edisi')->default(2);
            $table->boolean('unik_dosen_per_edisi')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('pengaturan_jurnal');
    }
};