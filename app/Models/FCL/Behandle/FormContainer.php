<?php

namespace App\Models\FCL\Behandle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ContainerFCL;

class FormContainer extends Model
{
    use HasFactory;
    protected $table = 'tform_fcl_behandle_container';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'form_id',
        'container_id',
        'nocontainer',
        'size',
        'ctr_type',
        'no_bl_awb',
        'tgl_bl_awb',
    ];

    public function cont()
    {
       return $this->belongsTo(ContainerFCL::class, 'container_id', 'id');
    }
}
