<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsSPPBKms extends Model
{
    use HasFactory;
    protected $table = 'tps_sppbkmsxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'sppb_id',
        'car',
        'jns_kms',
        'merk_kms',
        'jml_kms',
    ];
}
