<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use carbon\Carbon;

use DataTables;

class PengirimanController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function containerIndex()
    {
        $data['title'] = "Pengriman Data Container"; 
    }

    public function containerData(Request $request)
    {

    }
}
