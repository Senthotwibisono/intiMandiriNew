<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempCont extends Model
{
    use HasFactory;

    protected $table = 'container_temp';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'detil_id',
        'nocontainer',
        'size',
    ];
}
