<?php

namespace App\Models\Pengiriman\Coari;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoariKmsDetil extends Model
{
    use HasFactory;
    protected $table = 'tpscoarikmsdetailxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'coari_id',
        'manifest_id',
        'ref_number',
        'notally',
        'kd_dok',
        'kd_tps',
        'nm_angkut',
        'no_voy_flight',
        'call_sign',
        'tgl_tiba',
        'kd_gudang',
        'no_bl_awb',
        'tgl_bl_awb',
        'no_master_bl_awb',
        'tgl_master_bl_awb',
        'id_consignee',
        'consignee',
        'bruto',
        'no_bc11',
        'tgl_bc11',
        'no_pos_bc11',
        'cont_asal',
        'seri_kemas',
        'kd_kemas',
        'jml_kemas',
        'kd_timbun',
        'kd_dok_inout',
        'no_dok_inout',
        'tgl_dok_inout',
        'wk_inout',
        'kd_sar_angkut_inout',
        'no_pol',
        'pel_muat',
        'pel_transit',
        'pel_bongkar',
        'gudang_tujuan',
        'uid',
        'response',
        'status_tps',
        'nourut',
        'kode_kantor',
        'no_daftar_pabean',
        'tgl_daftar_pabean',
        'no_segel_bc',
        'tgl_segel_bc',
        'no_ijin_tps',
        'tgl_ijin_tps',
        'response_ipc',
        'status_tps_ipc',
        'kd_tps_asal',
        'tgl_entry',
        'jam_entry',
    ];
}
