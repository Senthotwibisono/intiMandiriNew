<?php

namespace App\Http\Controllers\cesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function plpIndex()
    {
        
    }
}
