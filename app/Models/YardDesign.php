<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YardDesign extends Model
{
    use HasFactory;
    protected $table = 'yard_block';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nomor', 
        'yard_block', 
        'max_slot', 
        'max_row', 
        'max_tier', 
    ];

    public function yardDetils()
    {
        return $this->hasMany(YardDetil::class, 'yard_id');
    }

}
