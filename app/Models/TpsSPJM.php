<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsSPJM extends Model
{
    use HasFactory;
    protected $table = 'tps_spjmxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'car',
        'kd_kantor',
        'tgl_pib',
        'no_pib',
        'no_spjm',
        'tgl_spjm',
        'npwp_imp',
        'nama_imp',
        'npwp_ppjk',
        'nama_ppjk',
        'gudang',
        'jml_cont',
        'no_bc11',
        'tgl_bc11',
        'no_pos_bc11',
        'fl_karantina',
        'nm_angkut',
        'no_voy_flight',
        'tgl_upload',
        'jam_upload',
        'no_dok',
        'tgl_dok',
        'flag',
    ];

}
