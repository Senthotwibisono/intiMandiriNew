<?php

namespace App\Models\FCL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\User;
use App\Models\JobOrderFCL as JobF;

class InvoiceHeader extends Model
{
    use HasFactory;
    protected $table = 'tinvoice_header_fcl';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'proforma_no',
        'invoice_no',
        'kd_tps_asal',
        'form_id',
        'job_id',
        'nobl',
        'tgl_bl_awb',
        'cust_id',
        'cust_name',
        'cust_alamat',
        'cust_npwp',
        'eta',
        'tglmasuk',
        'etd',
        'total_tps',
        'total_wms',
        'total',
        'admin',
        'ppn',
        'grand_total',
        'status',
        'uidCreate',
        'uidLunas',
        'lunas_at',
        'created_at',
        'kapal_voy',
    ];

    public function Cust()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'id');
    }
    
    public function userCreate()
    {
        return $this->belongsTo(User::class, 'uidCreate', 'id');
    }

    public function userLunas()
    {
        return $this->belongsTo(User::class, 'uidLunas', 'id');
    }

    public function job()
    {
        return $this->belongsTo(JobF::class, 'job_id', 'id');
    }
    
    public function form()
    {
        return $this->belongsTo(FormFCL::class, 'form_id', 'id');
    }

}
