<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RackTier extends Model
{
    use HasFactory;
    protected $table = 'rack_tier';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'rack_id', 
        'tier', 
        'barcode', 
        'jumlah_barang',  
    ];

    public function Rack()
    {
        return $this->belongsTo(PlacementManifest::class, 'rack_id', 'id');
    }
}
