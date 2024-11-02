<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoSegel extends Model
{
    use HasFactory;
    protected $table = 'photo_log_segel';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'log_id',
        'photo',
    ];

    public function log()
    {
        return $this->belongsTo(User::class, 'log_id', 'id');
    }
}
