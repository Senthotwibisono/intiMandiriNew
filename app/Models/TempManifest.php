<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempManifest extends Model
{
    use HasFactory;
    protected $table = 'manifest_temp';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'detil_id',
        'nohbl',
        'tgl_hbl',
        'shipper_id',
        'customer_id',
        'notifyparty_id',
        'marking',
        'quantity',
        'packing_id',
        'weight',
        'meas',
    ];
}
