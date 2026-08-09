<?php

namespace App\Http\Controllers\cesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\JobOrder as Job;
use App\Models\Container as Cont;
use App\Models\JobOrderFCL as JobF;
use App\Models\ContainerFCL as ContF;
use App\Models\Manifest;
use App\Models\Customer;

class FrontController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function plpIndex()
    {
        
    }

    public function trackingFailIndex($type, $gate)
    {
        $data['title'] = 'Tracking Fail';

        return view('cesa.tracking.fail', $data );
    }
}
