<?php

namespace App\Imports\formatBaru;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\Manifest;
use App\Models\TempManifest;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Packing;
use App\Models\Vessel;
use Auth;
use Carbon\Carbon;
use Str;

class dataMaster implements ToCollection,  WithHeadingRow
{
    /**
    * @param Collection $collection
    */

    protected $jobId;

    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    public function collection(Collection $rows)
    {
        // $headers = $rows->first()->keys()->toArray();
        // dd($headers);

        $firstRow = $rows->first();
        $jobId = $this->jobId;
        $job = Job::find($jobId);
        $no_bl = trim($firstRow['master_bl']);
        if (!is_null($job->nombl) && $job->nombl !== '' && $job->nombl != $no_bl) {
            throw new \Exception('NO MBL Tidak Sesuai');
        }
        $tanggal_bl = Carbon::createFromFormat('Y-d-m', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($firstRow['tanggal_bl'])->format('Y-m-d'));
        $job->update([
        'nombl' =>  $no_bl,
        'tgl_master_bl'=> $tanggal_bl,
        ]);

        
    }
}
