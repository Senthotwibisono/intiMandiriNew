<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsManualCont extends Model
{
    use HasFactory;
    protected $table = 'tps_dokmanualcontxml';
    protected $primaryKey = 'idm';
    public $timestamps = false;

    protected $fillable = [
        'manual_id',
        'id',
        'no_cont',
        'size',
        'jns_muat',
    ];
}
