<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacementManifest extends Model
{
    use HasFactory;
    protected $table = 'placement_manifest';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nomor', 
        'name', 
        'use_for', 
        'use', 
        'barcode',
        'jumlah_barang', 
    ];

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id', 'id');
    }
}
