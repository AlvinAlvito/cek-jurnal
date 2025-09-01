<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RumahJurnal extends Model
{
    protected $table = 'rumah_jurnal';
    protected $fillable = ['nama', 'link', 'deskripsi'];

    public function edisi(): HasMany
    {
        return $this->hasMany(EdisiRumahJurnal::class, 'rumah_jurnal_id');
    }

    public function pemakaian(): HasMany
    {
        return $this->hasMany(PemakaianRumahJurnal::class, 'rumah_jurnal_id');
    }
}
