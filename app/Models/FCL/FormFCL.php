<?php

namespace App\Models\FCL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\LokasiSandar; // Tambahkan ini
use App\Models\User; // Jika User tidak berada di namespace yang sama
use App\Models\Customer;

class FormFCL extends Model
{
    use HasFactory;
    protected $table = 'tform_invoice_fcl';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'lokasi_sandar_id',
        'nobl',
        'tgl_bl_awb',
        'cust_id',
        'eta',
        'etd',
        'status',
        'uid',
        'created_at',
        'type',
        'inv_id',
        'form_id',
    ];

    public function LokasiSandar()
    {
        return $this->belongsTo(LokasiSandar::class, 'lokasi_sandar_id', 'id');
    }

    public function Cust()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }

    public function oldInvoice()
    {
        if ($this->type == 'EXTEND') {
            return $this->belongsTo(InvoiceHeader::class, 'inv_id', 'id');
        }
    }

    public function oldForm()
    {
        if ($this->type == 'EXTEND') {
            return $this->belongsTo(FormFCL::class, 'form_id', 'id');
        }
    }
}
