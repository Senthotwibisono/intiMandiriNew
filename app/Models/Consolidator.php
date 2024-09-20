<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consolidator extends Model
{
    use HasFactory;
    protected $table = 'tconsolidator';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'namaconsolidator',
        'code',
        'notelp',
        'contactperson',
        'nocano',
        'tglakhirkontrak',
        'kontrak',
        'uid',
        'npwp',
        'ppn',
        'materai',
        'nppkp',
        'npwp1',
        'id_consolidator',
        'keterangan',
        'namaconsolidator1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
