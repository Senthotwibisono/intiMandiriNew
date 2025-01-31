<?php

namespace App\Models\FCL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\LokasiSandar; // Tambahkan ini
use App\Models\User; // Jika User tidak berada di namespace yang sama

class FormFCL extends Model
{
    use HasFactory;
    protected $table = 'tform_invoice_fcl';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'lokasi_sandar_id',
        'nobl',
        'tgl_bl_awb',
        'cust_id',
        'eta',
        'etd',
        'status',
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
