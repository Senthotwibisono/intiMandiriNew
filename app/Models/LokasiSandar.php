<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiSandar extends Model
{
    use HasFactory;
    protected $table = 'tlokasi_sandar';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'kd_tps_asal',
        'jabatan',
        'perusahaan_id',
        'pelabuhan_id',
        'kota',
        'negara_id',
        'uid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }

    public function pelabuhan()
    {
        return $this->belongsTo(Pelabuhan::class, 'pelabuhan_id', 'id');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'id');
    }
    
    public function negara()
    {
        return $this->belongsTo(Negara::class, 'negara_id', 'id');
    }
}
