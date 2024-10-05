<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsPabean extends Model
{
    use HasFactory;
    protected $table = 'tps_dokpabeanxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kd_dok_inout',
        'car',
        'no_dok_inout',
        'tgl_dok_inout',
        'no_daftar',
        'tgl_daftar',
        'kd_kantor',
        'kd_kantor_pengawas',
        'kd_kantor_bongkar',
        'npwp_imp',
        'nm_imp',
        'al_imp',
        'npwp_ppjk',
        'nm_ppjk',
        'al_ppjk',
        'nm_angkut',
        'no_voy_flight',
        'brutto',
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
        'fl_segel',
        'tgl_upload',
        'jam_upload',
    ];

    public function dokumen()
    {
        return $this->belongsTo(KodeDok::class, 'kd_dok_inout', 'kode');
    }
}
