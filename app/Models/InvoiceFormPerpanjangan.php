<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceFormPerpanjangan extends Model
{
    use HasFactory;
    protected $table = 'invoice_form_perpanjangan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'old_invoice_id',
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
        'massa1',
        'massa2',
        'massa3',
        'mekanik_y_n',
        'total',
        'admin',
        'pajak',
        'pajak_amount',
        'discount',
        'grand_total',
        'total_m',
        'admin_m',
        'pajak_m',
        'pajak_amount_m',
        'discount_m',
        'grand_total_m',
        'created_at',
        'uid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
    public function Old()
    {
        return $this->belongsTo(InvoiceHeader::class, 'old_invoice_id', 'id');
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
