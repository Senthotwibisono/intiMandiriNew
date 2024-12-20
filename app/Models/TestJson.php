<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestJson extends Model
{
    use HasFactory;
    protected $table = 'test_json';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'name',
        'type',
        'keterangan',
        'desk',
    ];
}
