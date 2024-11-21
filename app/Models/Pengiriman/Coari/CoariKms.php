<?php

namespace App\Models\Pengiriman\Coari;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoariKms extends Model
{
    use HasFactory;
    protected $table = 'tpscoarikmsxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'tgl_entry',
        'jam_entry',
        'ref_number',
        'uid',
        'nomor',
    ];
}
