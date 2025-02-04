<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSegel extends Model
{
    use HasFactory;
    protected $table = 'log_segel';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ref_id',
        'ref_type',
        'no_segel',
        'alasan',
        'keterangan',
        'action',
        'created_at',
        'updated_at',
        'uid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'ref_id', 'id');
    }

    public function fcl()
    {
        return $this->belongsTo(ContainerFCL::class, 'ref_id', 'id');
    }
}
