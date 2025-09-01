<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianRumahJurnal extends Model
{
    protected $table = 'pemakaian_rumah_jurnal';
    protected $fillable = [
        'rumah_jurnal_id','edisi_id','mahasiswa_id','dosen_pembimbing_id',
        'status','digunakan_pada','catatan'
    ];

    protected $casts = [
        'digunakan_pada' => 'datetime',
    ];

    // Relasi
    public function rumahJurnal(): BelongsTo
    {
        return $this->belongsTo(RumahJurnal::class, 'rumah_jurnal_id');
    }

    public function edisi(): BelongsTo
    {
        return $this->belongsTo(EdisiRumahJurnal::class, 'edisi_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function dosenPembimbing(): BelongsTo
    {
        return $this->belongsTo(DosenPembimbing::class, 'dosen_pembimbing_id');
    }

    // Scope bantu untuk status aktif (ikut hitung kuota)
    public function scopeAktif($q)
    {
        return $q->whereIn('status', ['pending','disetujui']);
    }
}
