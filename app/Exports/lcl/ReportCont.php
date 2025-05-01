<?php

namespace App\Exports\lcl;

use App\Models\Conttainer as Cont;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ReportCont implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $conts;

    public function __construct($conts)
    {
        $this->conts = $conts;
    }

    public function collection()
    {
        return $this->conts;
    }

    public function map($cont): array
    {
        return [
            $cont->job->nojoborder ?? '-',
            $cont->job->PLP->nm_angkut ?? '-',
            $cont->job->voy ?? '-',
            $cont->nocontainer ?? '-',
            $cont->size ?? '-',
            $cont->job->eta ?? '-',
            $cont->job->PLP->kd_tps_asal ?? '-',
            $cont->job->PLP->namaconsolidator ?? '-',
            $cont->job->noplp ?? '-',
            $cont->job->ttgl_plp ?? '-',
            $cont->job->PLP->no_bc11 ?? '-',
            $cont->job->PLP->tgl_bc11 ?? '-',
            null,
            $cont->tglmasuk ?? 'Belum Masuk',
            $cont->jammasuk ?? 'Belum Masuk',
            $cont->nopol ?? 'Belum Masuk',
            $cont->tglstripping ?? 'Belum Stripping',
            $cont->jamstripping ?? 'Belum Stripping',
            $cont->tglkeluar ?? 'Belum Keluar',
            $cont->jamkeluar ?? 'Belum Keluar', 
            $cont->nopol_mty ?? 'Belum Keluar', 
        ];
    }

    public function headings(): array
    {
        return [
            'No Job Order',
            'Nama Angkut',
            'No Voy',
            'No Container',
            'Size',
            'ETA',
            'TPS Asal',
            'Consolidator',
            'No PLP',
            'Tgl PLP',
            'No BC 1.1',
            'Tgl BC 1.1',
            'No POS BC 1.1',
            'Tgl Masuk',
            'Jam Masuk',
            'Nomor Polisi',
            'Tgl Stripping',
            'Jam Stripping',
            'Tgl Keluar',
            'Jam Keluar',
            'Nomor Polisi MTY',
        ];
    }
}
