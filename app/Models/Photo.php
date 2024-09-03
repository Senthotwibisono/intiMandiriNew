<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;
    protected $table = 'tphoto';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'master_id',
        'type',
        'tipe_gate',
        'action',
        'photo',
    ];

    public function container()
    {
        return $this->belongsTo(Container::class, 'master_id', 'id');
    }

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'master_id', 'id');
    }
}
