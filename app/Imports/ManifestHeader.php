<?php

namespace App\Imports;

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

class ManifestHeader implements ToCollection,  WithHeadingRow
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

        $no_bc_11 = trim($firstRow['no_bc_11']) ?? null;
        $tgl_bc_11 = !empty($firstRow['tgl_bc_11']) ? Carbon::createFromFormat('d-m-Y', trim($firstRow['tgl_bc_11']))->format('Y-m-d') : null;
        $nama_sarana_angkut = trim($firstRow['nama_sarana_angkut']) ?? null;
        $call_sign = trim($firstRow['call_sign']) ?? null;

        $oldVes = Vessel::where('name', $nama_sarana_angkut)->first();
        if ($oldVes) {
            $ves = $oldVes;
        }else {
            $ves = Vessel::create([
                'name' => $nama_sarana_angkut,
                'call_sign' => $call_sign ?? null,
            ]);
        }

        $jobId = $this->jobId;
        $job = Job::find($jobId);
        $job->update([
            'tno_bc11' => $no_bc_11 ?? null,
            'ttgl_bc11' => $tgl_bc_11 ?? null,
            'voy' => trim($firstRow['no_voyage_arrival']) ?? null,
            'vessel' => $ves->id ?? null,
            'callsign' => $call_sign ?? null,
        ]);
    }
}
