<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KapasitasGudang extends Model
{
    use HasFactory;
    protected $table = 'kapasitas_gudang';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable =
    [
        'kapasitas',
    ];
}
