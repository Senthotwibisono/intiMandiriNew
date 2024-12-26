<?php

namespace App\Imports\formatBaru;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class manifestMain implements WithMultipleSheets
{
    /**
    * @param Collection $collection
    */
    public function sheets(): array
    {
        return [
            'Kontainer' => new ManifestCont($this->jobId),
            'Detil' => new ManifestExcel($this->jobId),
            'Barang' => new ManifestBarang($this->jobId),
            'Master Entry' => new ManifestEntry($this->jobId),
            'Header' => new ManifestHeader($this->jobId),
        ];
    }
}
