<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsPLPdetail extends Model
{
    use HasFactory;
    protected $table = 'tps_responplptujuandetailxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'plp_id',
        'tgl_upload',
        'no_plp',
        'tgl_plp',
        'no_cont',
        'uk_cont',
        'jns_cont',
        'no_bc11',
        'tgl_bc11',
        'no_pos_bc11',
        'consignee',
        'jns_kms',
        'jml_kms',
        'no_bl_awb',
        'tgl_bl_awb',
        'regcode',
        'insdate',
        'regcodedata',
        'flag_spk',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
