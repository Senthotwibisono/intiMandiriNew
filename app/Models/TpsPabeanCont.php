<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsPabeanCont extends Model
{
    use HasFactory;
    protected $table = 'tps_dokpabeancontxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'pabean_id',
        'car',
        'no_cont',
        'ukr_cont',
        'size',
        'jns_muat',
    ];
}
