<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceFormTarif extends Model
{
    use HasFactory;
    protected $table = 'invoice_form_tarif';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'manifest_id',
        'form_id',
        'tarif_id',
        'harga',
        'jumlah',
        'jumlah_hari',
        'total',
        'mekanik_y_n',
    ];


    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id', 'id');
    }

    public function Form()
    {
        return $this->belongsTo(InvoiceForm::class, 'form_id', 'id');
    }
    
    public function Tarif()
    {
        return $this->belongsTo(MasterTarif::class, 'tarif_id', 'id');
    }
}
