<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ManifestMaster implements WithMultipleSheets
{
    protected $jobId;

    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    public function sheets(): array
    {
        return [
            'Kontainer' => new ManifestCont($this->jobId),
            'Detil' => new ManifestExcel($this->jobId),
            'Barang' => new ManifestBarang($this->jobId),
            'Master Entry' => new ManifestEntry($this->jobId),
        ];
    }
}

