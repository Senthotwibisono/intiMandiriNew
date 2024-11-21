<?php

namespace App\Models\Pengiriman\Coari;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoariCont extends Model
{
    use HasFactory;
    protected $table = 'tpscoaricontxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ref_number',
        'tgl_entry',
        'jam_entry',
        'uid',
        'nomor',
        'status_ref',
        'ref_number_revisi',
        'flag_revisi',
        'tgl_revisi',
    ];
}
