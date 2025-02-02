<?php

namespace App\Models\FCL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Customer;
use App\Models\user;
use App\Models\ContainerFCL as ContF;

class FormContainerFCL extends Model
{
    use HasFactory;
    protected $table = 'tform_container_fcl';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'form_id',
        'container_id',
        'size',
        'ctr_type',
        'behandle_yn',
        'uid',
        'created_at',
    ];

    public function form()
    {
        return $this->belongsTo(FormFCL::class, 'form_id', 'id');
    }

    public function cont()
    {
        return $this->belongsTo(ContF::class, 'container_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
