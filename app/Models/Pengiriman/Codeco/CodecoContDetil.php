<?php

namespace App\Models\Pengiriman\Codeco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodecoContDetil extends Model
{
    use HasFactory;
    protected $table = 'tpscodecocontdetailxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'codeco_id',
        'cont_id',
        'ref_number',
        'nojoborder',
        'kd_dok',
        'kd_tps',
        'nm_angkut',
        'no_voy_flight',
        'call_sign',
        'tgl_tiba',
        'kd_gudang',
        'no_cont',
        'uk_cont',
        'no_segel',
        'jns_cont',
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
        'kd_timbun',
        'kd_dok_inout',
        'no_dok_inout',
        'tgl_dok_inout',
        'wk_inout',
        'kd_sar_angkut_inout',
        'no_pol',
        'fl_cont_kosong',
        'iso_code',
        'pel_muat',
        'pel_transit',
        'pel_bongkar',
        'gudang_tujuan',
        'uid',
        'nourut',
        'response',
        'status_tps',
        'kode_kantor',
        'no_daftar_pabean',
        'tgl_daftar_pabean',
        'no_segel_bc',
        'tgl_segel_bc',
        'no_ijin_tps',
        'tgl_ijin_tps',
        'response_ipc',
        'status_tps_ipc',
        'nosppb',
        'tglsppb',
        'flag_revisi',
        'tgl_revisi',
        'tgl_revisi_update',
        'kd_tps_asal',
        'response_mal0',
        'status_tps_mal0',
        'tgl_entry',
        'jam_entry',
    ];
}
