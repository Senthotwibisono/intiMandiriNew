<?php

namespace App\Models\FCL\Behandle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancel extends Model
{
    use HasFactory;
    protected $table = 'theader_fcl_behandle_cancel';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'invoice_no',
    ];
}
