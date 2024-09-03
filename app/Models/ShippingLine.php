<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingLine extends Model
{
    use HasFactory;
    protected $table = 'tshipping_line';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'shipping_line',
        'vessel_id',
        'email',
        'contact',
        'keterangan',
        'uid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id', 'id');
    }
}
