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

class dataKapal implements ToCollection,  WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    protected $jobId;
    protected static $data = [];

    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    public function collection(Collection $rows)
    {
        // $headers = $rows->first()->keys()->toArray();
        // dd($headers);

        $firstRow = $rows->first();
        $nama_sarana_angkut = trim($firstRow['nama_sarana_pengangkut']) ?? null;
        $oldVes = Vessel::where('name', $nama_sarana_angkut)->first();
        if ($oldVes) {
            $ves = $oldVes;
        }else {
            $ves = Vessel::create([
                'name' => $nama_sarana_angkut,
            ]);
        }

        $jobId = $this->jobId;
        $job = Job::find($jobId);

        if (!is_null($job->vessel) && $job->vessel != $ves->id) {
            throw new \Exception('Data Kapal Tidak Sesuai');
        }
        $job->update([
            'voy' => trim($firstRow['nomor_voyage']) ?? null,
            'vessel' => $ves->id ?? null,
        ]);
    }
}
