<?php

namespace App\Models\FCL\Behandle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Customer;

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
        'customer_id',
        'created_at',
        'uid',
    ];

    public function user()
    {
       return $this->belongsTo(User::class, 'uid', 'id');
    }

    public function cust()
    {
       return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
