<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCSFDetil extends Model
{
    use HasFactory;
    protected $table = 'invoice_csf_detil';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'header_id',
        'kode_tarif',
        'tarif_dasar',
        'qty',
        'hari',
        'nilai',
        'satuan',
    ];

    public function header()
    {
        return $this->belongsTo(InvoiceCSF::class, 'header_id', 'id');
    }

    public function desc()
    {
        return $this->belongsTo(TarifCFS::class, 'kode_tarif', 'kode_bill');
    }
}
