<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTarif extends Model
{
    use HasFactory;
    protected $table = 'ttarif';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kode_tarif',
        'nama_tarif',
        'tarif_dasar',
        'jenis_storage',
        'day',
        'period',
        'created_by',
        'updated_by',
        'created_at',
    ];
}
