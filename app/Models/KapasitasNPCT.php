<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KapasitasNPCT extends Model
{
    use HasFactory;
    protected $table = 'kapasitas_npct';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 
        'warehouse_type', 
        'warehouse_code',   
        'yor',   
        'capacity', 
        'response'  
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
