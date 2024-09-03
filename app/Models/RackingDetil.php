<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RackingDetil extends Model
{
    use HasFactory;
    protected $table = 'rack_detil';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'rack_id', 
        'manifest_id', 
        'item_id', 
        'status', 
        'input_date', 
        'move_date', 
        'out_date', 
    ];

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id', 'id');
    }

    public function Item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
    
    public function Rack()
    {
        return $this->belongsTo(PlacementManifest::class, 'rack_id', 'id');
    }
}
