<?php

namespace App\Exports\invoice;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
class ReportProforma implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $haeaders;

    public function __construct($haeaders)
    {
        $this->haeaders = $haeaders;
    }

    public function collection()
    {
        return $this->haeaders;
    }

    public function map($header): array
    {
        return [
            $header->order_no,
            $header->order_at,
            $header->manifest->cont->nocontainer,
            $header->manifest->nohbl,
            $header->Form->cbm,
            $header->manifest->quantity,
            $header->form->jumlah_hari,
            $header->total ?? 0,
            $header->admin ?? 0,
            $header->discount ?? 0,
            $header->ppn_amount ?? 0,
            $header->grand_total ?? 0,
            $header->customer->name,
           
        ];
    }

    public function headings(): array
    {
        return [
            'Order No',
            'Created At',
            'Container No',
            'HBL',
            'CBM',
            'KMS',
            'Hari',
            'Total',
            'Admin',
            'Discount',
            'PPN',
            'Grand Total',
            'Customer',
        ];
    }
}
