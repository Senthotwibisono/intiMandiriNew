<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Container as Cont;
use App\Models\Joborder as Job;
use App\Models\Manifest;
use App\Models\TempManifest;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Packing;
use Auth;
use Carbon\Carbon;
use Str;

class ManifestEntry implements ToCollection,  WithHeadingRow
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
        // dd($headers); // This will output the column headers
        $firstRow = $rows->first();
        $jobId = $this->jobId;
        $job = Job::find($jobId);
        $job->update([
        'nombl' => trim($firstRow['no_master_blawb']),
        'tgl_master_bl'=> Carbon::createFromFormat('d-m-Y', trim($firstRow['tgl_master_blawb']))->format('Y-m-d'),
        ]);
    }
}
