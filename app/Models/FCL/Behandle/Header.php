<?php

namespace App\Models\FCL\Behandle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Customer;

class Header extends Model
{
    use HasFactory;
    protected $table = 'theader_fcl_behandle';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'form_id',
        'proforma_no',
        'invoice_no',
        'no_spjm',
        'tgl_spjm',
        'customer_id',
        'customer_name',
        'customer_alamat',
        'customer_npwp',
        'status',
        'admin',
        'total',
        'ppn',
        'grand_total',
        'order_by',
        'order_at',
        'lunas_at',
        'lunas_by',
        'cancel_at',
        'cancel_by',
        'flag_hidden',
        'hidden_by',
        'hidden_at',
    ];

    public function form()
    {
       return $this->belongsTo(Form::class, 'form_id', 'id');
    }

    public function cust()
    {
       return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function order()
    {
       return $this->belongsTo(User::class, 'order_by', 'id');
    }

    public function lunas()
    {
       return $this->belongsTo(User::class, 'lunas_by', 'id');
    }

    public function cancel()
    {
       return $this->belongsTo(User::class, 'cancel_by', 'id');
    }
}
