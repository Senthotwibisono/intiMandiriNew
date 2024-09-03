<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsPLP extends Model
{
    use HasFactory;
    protected $table = 'tps_responplptujuanxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'joborder_id',
        'tgl_upload',
        'upload_date',
        'upload_time',
        'kd_kantor',
        'kd_tps',
        'kd_tps_asal',
        'gudang_tujuan',
        'no_plp',
        'tgl_plp',
        'call_sign',
        'nm_angkut',
        'no_voy_flight',
        'tgl_tiba',
        'no_surat',
        'tgl_surat',
        'no_bc11',
        'tgl_bc11',
        'uid',
        'yor_tps_asal',
        'yor_tps_tujuan',
        'alasan',
        'lampiran',
        'jns_cont',
        'flag_spk',
        'consolidator_id',
        'namaconsolidator',
        'apl',
        'kd_tps_tujuan',
        'ref_number',
        'gudang_asal',
        'alasan_reject',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
