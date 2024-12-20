<?php

namespace App\Models\Pengiriman;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefNumber extends Model
{
    use HasFactory;
    protected $table = 'ref_number';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'main',
        'tahun',
        'bulan',
        'tanggal',
        'nomor',
    ];
}
