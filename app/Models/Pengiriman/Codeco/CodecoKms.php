<?php

namespace App\Models\Pengiriman\Codeco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodecoKms extends Model
{
    use HasFactory;
    protected $table = 'tpscodecokmsxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nojoborder',
        'tgl_entry',
        'jam_entry',
        'ref_number',
        'uid',
        'nomor',
    ];
}
