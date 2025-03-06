<?php

namespace App\Models\FCL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LokasiSandar; // Tambahkan ini
use App\Models\User; // Jika User tidak berada di namespace yang sama


class MTarifTPS extends Model
{
    use HasFactory;
    protected $table = 'ttarif_tps_fcl';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'lokasi_sandar_id',
        'size',
        'type',
        'tarif_dasar_massa',
        'massa2',
        'massa3',
        'lift_on',
        'hyro_scan',
        'perawatan_it',
        'gate_pass',
        'refeer',
        'monitoring',
        'surcharge',
        'admin',
        'uid',
        'created_at',
    ];

    public function LokasiSandar()
    {
        return $this->belongsTo(LokasiSandar::class, 'lokasi_sandar_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
