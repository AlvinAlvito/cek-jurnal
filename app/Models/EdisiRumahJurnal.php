<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdisiRumahJurnal extends Model
{
    protected $table = 'edisi_rumah_jurnal';
    protected $fillable = ['rumah_jurnal_id', 'tahun', 'bulan', 'kuota', 'label'];

    public function rumahJurnal(): BelongsTo
    {
        return $this->belongsTo(RumahJurnal::class, 'rumah_jurnal_id');
    }

    public function pemakaian(): HasMany
    {
        return $this->hasMany(PemakaianRumahJurnal::class, 'edisi_id');
    }

    // Helper opsional: sisa slot (hanya menghitung pending + disetujui)
    public function getSisaSlotAttribute(): int
    {
        $terpakai = $this->pemakaian()
            ->whereIn('status', ['pending','disetujui'])
            ->count();

        return max(0, (int)$this->kuota - $terpakai);
    }

    // Helper opsional: nama bulan (Indonesia)
    public function getNamaBulanAttribute(): string
    {
        $bulan = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        return $bulan[$this->bulan] ?? (string)$this->bulan;
    }
}
