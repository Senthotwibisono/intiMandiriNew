<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsSPPBBC23Kms extends Model
{
    use HasFactory;
    protected $table = 'tps_sppbbc23kmsxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'sppb23_id',
        'car',
        'jns_kms',
        'merk_kms',
        'jml_kms',
    ];
}
