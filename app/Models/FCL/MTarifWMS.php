<?php

namespace App\Models\FCL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; 

class MTarifWMS extends Model
{
    use HasFactory;
    protected $table = 'ttarif_wms_fcl';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'size',
        'type',
        'paket_plp',
        'tarif_dasar_massa',
        'massa',
        'lift_on',
        'lift_off',
        'gate_pass',
        'refeer',
        'monitoring',
        'surcharge',
        'behandle',
        'admin',
        'admin_behandle',
        'uid',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
