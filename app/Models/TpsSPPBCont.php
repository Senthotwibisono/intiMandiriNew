<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsSPPBCont extends Model
{
    use HasFactory;
    protected $table = 'tps_sppbcontxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'sppb_id',
        'car',
        'no_cont',
        'size',
        'jns_muat',
    ];
}
