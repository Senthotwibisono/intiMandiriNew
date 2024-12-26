<?php

namespace App\Imports\formatBaru;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class manifestMain implements WithMultipleSheets
{
    /**
    * @param Collection $collection
    */

    protected $jobId;

    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    public function sheets(): array
    {
        return [
            'NVOCC' => new dataKapal($this->jobId),
            'MASTER' => new dataMaster($this->jobId),
            // 'BL DOKUMEN' => new dataBC11($this->jobId),
            'BL' => new dataManifest($this->jobId),
            'BL HS' => new dataDetil($this->jobId),
            'BL PETIKEMAS TERANGKUT' => new dataContainer($this->jobId),
        ];
    }
}
