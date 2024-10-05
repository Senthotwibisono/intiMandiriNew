<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepoMty extends Model
{
    use HasFactory;
    protected $table = 'tdepo_mty';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'uid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
