<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCSF extends Model
{
    protected $table = 'invoice_csf';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'manifest_id',
        'no_bl_awb',
        'consignee',
        'npwp_consignee',
        'no_order',
        'jenis_billing',
        'jenis_bayar',
        'jenis_transaksi',
        'subtotal',
        'ppn',
        'total',
        'weight',
        'measure',
        'jns_kms',
        'merk_kms',
        'jml_kms',
        'tarif',
        'status',
        'rencana_keluar',
        'lunas_at',
        'cancel_at',
        'created_at',
    ];

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id', 'id');
    }
}
