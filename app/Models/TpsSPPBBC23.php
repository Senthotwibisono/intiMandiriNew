<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsSPPBBC23 extends Model
{
    use HasFactory;
    protected $table = 'tps_sppbbc23xml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'car',
        'no_sppb',
        'tgl_sppb',
        'nojoborder',
        'kd_kantor_pengawas',
        'kd_kantor_bongkar',
        'no_pib',
        'tgl_pib',
        'nama_imp',
        'npwp_imp',
        'alamat_imp',
        'npwp_ppjk',
        'nama_ppjk',
        'alamat_ppjk',
        'nm_angkut',
        'no_voy_flight',
        'bruto',
        'netto',
        'gudang',
        'status_jalur',
        'jml_cont',
        'no_bc11',
        'tgl_bc11',
        'no_pos_bc11',
        'no_bl_awb',
        'tgl_bl_awb',
        'no_master_bl_awb',
        'tgl_master_bl_awb',
        'tgl_upload',
        'jam_upload',
    ];
}
