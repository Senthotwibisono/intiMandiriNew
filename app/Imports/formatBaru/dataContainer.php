<?php

namespace App\Imports\formatBaru;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\TempCont;
use Auth;
use Carbon\Carbon;

class dataContainer implements ToCollection, WithHeadingRow
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
        $jobId = $this->jobId;
        foreach ($rows as $row) {
            $detil_id = trim($row['id_bl'] ?? '');
            $nocontainer = trim($row['nomor_container'] ?? '');
            $size = trim($row['ukuran_container'] ?? '');

            $cont = TempCont::create([
                'job_id' => $jobId,
                'detil_id' => $detil_id,
                'nocontainer' => $nocontainer,
                'size' => $size,
            ]);
        }

        
    }
}
