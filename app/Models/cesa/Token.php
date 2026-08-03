<?php

namespace App\Models\cesa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;
    protected $table = 'cesa_token';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'access_token',
        'refresh_token',
        'expired_at',
    ];
}
