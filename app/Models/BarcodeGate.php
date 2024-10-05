<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeGate extends Model
{
    use HasFactory;
    protected $table = 'barcode_autogate';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ref_id', 
        'ref_type',
        'ref_action',
        'ref_number',
        'barcode',
        'time_in',
        'time_out',
        'status',
        'cancel',
        'expired',
        'photo_in',
        'photo_out',
        'uid',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }

    public function cont()
    {
        return $this->belongsTo(Container::class, 'ref_id', 'id');
    }

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'ref_id', 'id');
    }
}
