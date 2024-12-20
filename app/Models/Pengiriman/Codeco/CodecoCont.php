<?php

namespace App\Models\Pengiriman\Codeco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodecoCont extends Model
{
    use HasFactory;
    protected $table = 'tpscodecocontxml';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nojoborder',
        'ref_number',
        'tgl_entry',
        'jam_entry',
        'uid',
        'nomor',
        'status_ref',
        'ref_number_revisi',
        'flag_revisi',
        'tgl_revisi',
    ];
}
