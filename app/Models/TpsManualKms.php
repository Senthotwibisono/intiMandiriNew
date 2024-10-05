<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsManualKms extends Model
{
    use HasFactory;
    protected $table = 'tps_dokmanualkmsxml';
    protected $primaryKey = 'idm';
    public $timestamps = false;

    protected $fillable = [
        'manual_id',
        'id',
        'jns_kms',
        'merk_kms',
        'jml_kms',
    ];
}
