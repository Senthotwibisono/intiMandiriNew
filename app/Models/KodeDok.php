<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeDok extends Model
{
    use HasFactory;
    protected $table = 'kode_dok';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
