<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YardDetil extends Model
{
    use HasFactory;
    protected $table = 'yard_detail';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'yard_id',  
        'slot', 
        'row', 
        'tier', 
        'cont_id',
        'active',
        
    ];

    public function cont()
    {
        return $this->belongsTo(Container::class, 'cont_id', 'id');
    }

    public function yb()
    {
        return $this->belongsTo(YardDesign::class, 'yard_id', 'id');
    }
}
