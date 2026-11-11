<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrean extends Model
{
    protected $table = 'antrean';
    protected $guarded = [];
    protected $casts = [
        'diterbitkan_at' => 'datetime',
        'dimulai_at' => 'datetime',
        'selesai_at' => 'datetime',
        'dipanggil_at' => 'datetime',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function scopeAktif($q)
    {
        return $q->whereIn('status', ['menunggu', 'dilayani']);
    }
}