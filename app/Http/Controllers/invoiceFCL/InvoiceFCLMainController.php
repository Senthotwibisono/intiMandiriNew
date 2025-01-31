<?php

namespace App\Http\Controllers\invoiceFCL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

use App\Models\Customer;

use App\Models\LokasiSandar;


class InvoiceFCLMainController extends Controller
{
    
    public function dashboardInvoiceFCL()
    {

        $data['title'] = 'Dashboard Invoice (FCL)';

        return view('invoiceFCL.dashboard', $data);
    }

    public function indexMasterTarif()
    {
        $data['title'] = 'Master Tarif';
        $data['lokasiSandar'] = LokasiSandar::orderBy('kd_tps_asal', 'asc')->get();

        return view('invoiceFCL.master.tarif', $data);
    }

    public function indexForm()
    {
        $data['title'] = 'Form Invoice - FCL';

        return view('invoiceFCL.form.index', $data);
    }

}
