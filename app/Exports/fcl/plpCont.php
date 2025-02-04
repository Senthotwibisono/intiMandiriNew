<?php

namespace App\Exports\fcl;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Schema;
use App\Models\ContainerFCL as Contf;

class plpCont implements FromCollection, WithHeadings
{
    protected $joborder_id;

    public function __construct($joborder_id)
    {
        $this->joborder_id = $joborder_id;
    }

    /**
     * Mengambil semua data berdasarkan joborder_id
     */
    public function collection()
    {
        return Contf::where('joborder_id', $this->joborder_id)->get();
    }

    /**
     * Mengambil header otomatis berdasarkan struktur tabel
     */
    public function headings(): array
    {
        return Schema::getColumnListing((new Contf)->getTable());
    }
}
