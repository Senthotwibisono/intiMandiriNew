<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceForm extends Model
{
    use HasFactory;
    protected $table = 'invoice_form';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'manifest_id',
        'customer_id',
        'cbm',
        'status',
        'type',
        'time_in',
        'expired_date',
        'jumlah_hari',
        'period',
        'hari_period',
        'total',
        'admin',
        'pajak',
        'pajak_amount',
        'discount',
        'grand_total',
        'created_at',
        'uid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id', 'id');
    }
    public function Customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
