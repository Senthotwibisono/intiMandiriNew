<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsSPJMKms extends Model
{
    use HasFactory;
    protected $table = 'tps_spjmkmsxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'spjm_id',
        'car',
        'jns_kms',
        'merk_kms',
        'jml_kms',
        'fl_periksa',
    ];
}
