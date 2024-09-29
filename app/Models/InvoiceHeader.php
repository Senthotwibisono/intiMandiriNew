<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceHeader extends Model
{
    use HasFactory;
    protected $table = 'invoice_header';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'form_id',
        'manifest_id',
        'customer_id',
        'judul_invoice',
        'invoice_no',
        'order_no',
        'type',
        'time_in',
        'expired_date',
        'total',
        'admin',
        'discount',
        'pajak',
        'pajak_amount',
        'grand_total',
        'status',
        'order_at',
        'kasir_id',
        'piutang_at',
        'kasir_piutang_id',
        'lunas_at',
        'kasir_lunas_id',
        'no_hp',
        'ktp',
        'mekanik_y_n'
    ];


    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id', 'id');
    }
    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id', 'id');
    }
    public function kasirP()
    {
        return $this->belongsTo(User::class, 'kasir_piutang_id', 'id');
    }
    public function kasirL()
    {
        return $this->belongsTo(User::class, 'kasir_lunas_id', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function Form()
    {
        if ($this->type === 'P') {
            return $this->belongsTo(InvoiceFormPerpanjangan::class, 'form_id', 'id');
        }
    
        return $this->belongsTo(InvoiceForm::class, 'form_id', 'id');
    }
    
    public function Tarif()
    {
        return $this->belongsTo(MasterTarif::class, 'tarif_id', 'id');
    }
}
