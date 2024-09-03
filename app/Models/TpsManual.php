<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsManual extends Model
{
    use HasFactory;
    protected $table = 'tps_dokmanualxml';
    protected $primaryKey = 'idm';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'kd_kantor',
        'kd_dok_inout',
        'no_dok_inout',
        'tgl_dok_inout',
        'id_consignee',
        'consignee',
        'npwp_ppjk',
        'nama_ppjk',
        'nm_angkut',
        'no_voy_flight',
        'kd_gudang',
        'jml_cont',
        'no_bc11',
        'tgl_bc11',
        'no_pos_bc11',
        'no_bl_awb',
        'tgl_bl_awb',
        'fl_segel',
        'tgl_upload',
        'jam_upload',
    ];

    public function dokumen()
    {
        return $this->belongsTo(KodeDok::class, 'kd_dok_inout', 'kode');
    }
}
