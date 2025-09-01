<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $fillable = ['nama', 'nim'];

    public function pemakaian(): HasMany
    {
        return $this->hasMany(PemakaianRumahJurnal::class, 'mahasiswa_id');
    }
}
