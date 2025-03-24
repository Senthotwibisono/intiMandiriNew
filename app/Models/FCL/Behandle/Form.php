<?php

namespace App\Models\FCL\Behandle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Form extends Model
{
    use HasFactory;
    protected $table = 'tform_fcl_behandle';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'no_spjm',
        'tgl_spjm',
        'status',
        'created_at',
        'uid',
    ];

    public function user()
    {
       return $this->belongsTo(User::class, 'uid', 'id');
    }
}
