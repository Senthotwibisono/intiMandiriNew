<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeteranganPhoto extends Model
{
    use HasFactory;
    protected $table = 'tketerangan_photo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'tipe', 
        'kegiatan', 
        'keterangan',   
    ];
}
