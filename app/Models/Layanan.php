<?php

namespace App\Models;

use App\Models\Antrean;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $guarded = [];

    public function antrean()
    {
        return $this->hasMany(Antrean::class);
    }
}