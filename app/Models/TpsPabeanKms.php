<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpsPabeanKms extends Model
{
    use HasFactory;
    protected $table = 'tps_dokpabeankmsxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'pabean_id',
        'car',
        'jns_kms',
        'jml_kms',
    ];
}
