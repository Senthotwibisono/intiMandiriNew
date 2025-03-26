<?php

namespace App\Models\FCL\Behandle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detil extends Model
{
    use HasFactory;
    protected $table = 'tdetil_fcl_behandle';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'invoice_id',
        'size',
        'type',
        'tarif',
        'jumlah',
        'total',
    ];

    public function header()
    {
       return $this->belongsTo(Header::class, 'form_id', 'id');
    }
}
