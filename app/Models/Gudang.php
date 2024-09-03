<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;
    protected $table = 'tpsgudang';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'kode_kantor',
        'uid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
