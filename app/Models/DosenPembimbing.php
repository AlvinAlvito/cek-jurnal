<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DosenPembimbing extends Model
{
    protected $table = 'dosen_pembimbing';
    protected $fillable = ['nama'];

    public function pemakaian(): HasMany
    {
        return $this->hasMany(PemakaianRumahJurnal::class, 'dosen_pembimbing_id');
    }
}
