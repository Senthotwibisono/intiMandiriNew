<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $table = 'titem';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'manifest_id',
        'barcode',
        'name',
        'nomor',
        'stripping',
        'stripping_date',
        'stripping_time',
        'uid',
        'lokasi_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id', 'id');
    }
    public function Rack()
    {
        return $this->belongsTo(PlacementManifest::class, 'lokasi_id', 'id');
    }
}
