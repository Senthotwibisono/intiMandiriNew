<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsSPJMCont extends Model
{
    use HasFactory;
    protected $table = 'tps_spjmcontxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'spjm_id',
        'car',
        'no_cont',
        'size',
        'fl_periksa',
    ];
}
