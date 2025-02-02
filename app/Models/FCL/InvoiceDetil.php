<?php

namespace App\Models\FCL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetil extends Model
{
    use HasFactory;
    protected $table = 'tinvoice_detil_fcl';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'form_id',
        'invoice_id',
        'tps',
        'keterangan',
        'size',
        'type',
        'tarif_dasar',
        'satuan',
        'jumlah',
        'jumlah_hari',
        'total',
    ];

    public function form()
    {
        return $this->belongsTo(FormFCL::class, 'form_id', 'id');
    }

    public function invoice()
    {
        return $this->belongsTo(InvoiceHeader::class, 'invoice_id', 'id');
    }
    
}
