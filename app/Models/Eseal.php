<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eseal extends Model
{
    use HasFactory;
    protected $table = 'teseal';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'keterangan',
        'uid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
