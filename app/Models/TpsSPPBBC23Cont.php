<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsSPPBBC23Cont extends Model
{
    use HasFactory;
    protected $table = 'tps_sppbbc23contxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'sppb23_id',
        'car',
        'no_cont',
        'size',
        'jns_muat',
    ];
}
