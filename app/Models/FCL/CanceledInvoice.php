<?php

namespace App\Models\FCL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanceledInvoice extends Model
{
    use HasFactory;
    protected $table = 'tinvoice_fcl_canceled_inv_no';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'invoice_no',
    ];
}
