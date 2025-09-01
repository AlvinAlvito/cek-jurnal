<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('edisi_rumah_jurnal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rumah_jurnal_id')->constrained('rumah_jurnal')->cascadeOnDelete();
            $table->smallInteger('tahun');
            $table->tinyInteger('bulan'); // 1-12
            $table->unsignedTinyInteger('kuota')->default(2);
            $table->string('label', 32)->nullable();
            $table->timestamps();

            $table->unique(['rumah_jurnal_id', 'tahun', 'bulan']);
            $table->index(['rumah_jurnal_id', 'tahun', 'bulan']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('edisi_rumah_jurnal');
    }
};
