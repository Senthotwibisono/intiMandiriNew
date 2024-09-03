<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPJK extends Model
{
    use HasFactory;
    protected $table = 'tppjk';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'phone',
        'uid',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
