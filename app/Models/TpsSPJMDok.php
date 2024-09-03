<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsSPJMDok extends Model
{
    use HasFactory;
    protected $table = 'tps_spjmdokxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'spjm_id',
        'car',
        'jns_dok',
        'no_dok',
        'tgl_dok',
    ];
}
