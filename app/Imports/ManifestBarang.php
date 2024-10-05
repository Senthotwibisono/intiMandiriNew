<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\TempBarang;
use Auth;
use Carbon\Carbon;

class ManifestBarang implements ToCollection, WithHeadingRow
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
    //    $headers = $rows->first()->keys()->toArray();
    //     dd($headers); // This will output the column headers
        $jobId = $this->jobId;
        foreach ($rows as $row) {
            $detil_id = trim($row['id_detil'] ?? '');
            $descofgoods = trim($row['uraian_barang'] ?? '');

            $cont = TempBarang::create([
                'detil_id' => $detil_id,
                'descofgoods' => $descofgoods,
            ]);
        }
    }
}
