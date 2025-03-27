<?php

namespace App\Exports\lcl;

use App\Models\Manifest;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ReportManifest implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $manifests;

    public function __construct($manifests)
    {
        $this->manifests = $manifests;
    }

    public function collection()
    {
        return $this->manifests;
    }

    public function map($manifest): array
    {
        return[
            $manifest->cont->job->nojoborder ?? '-',
            $manifest->cont->job->PLP->nm_angkut ?? '-',
            $manifest->cont->nocontainer ?? '-',
            $manifest->cont->size ?? '-',
            $manifest->cont->job->eta ?? '-',
            $manifest->cont->job->PLP->kd_tps_asal ?? '-',
            $manifest->cont->job->PLP->namaconsolidator ?? '-',
            $manifest->nohbl ?? '-',
            $manifest->tgl_hbl ?? '-',
            $manifest->customer->name ?? '',
            $manifest->quantity,
            $manifest->packing->code ?? '',
            $manifest->weight ?? '-',
            $manifest->meas ?? '-',
            $manifest->cont->job->noplp ?? '-',
            $manifest->cont->job->ttgl_plp ?? '-',
            $manifest->cont->job->PLP->no_bc11 ?? '-',
            $manifest->cont->job->PLP->tgl_bc11 ?? '-',
            null,
            $manifest->cont->tglmasuk ?? 'Belum Masuk',
            $manifest->cont->jammasuk ?? 'Belum Masuk',
            $manifest->cont->nopol ?? 'Belum Masuk',
            $manifest->tglstripping ?? 'Belum Stripping',
            $manifest->jamstripping ?? 'Belum Stripping',
            $manifest->tglrelease ?? 'Belum Keluar',
            $manifest->jamrelease ?? 'Belum Keluar',
            $manifest->dokumen->name ?? 'Belum Tersedia',
            $manifest->no_dok ?? '-',
            $manifest->tgl_dok ?? '-',
            $manifest->mostItemsLocation()->Rack->name ?? 'Location not found',
            $manifest->descofgoods ?? '-',
            $manifest->lamaTimbun(),
        ];
    }

    public function headings(): array
    {
        return [
            'No Job Order',
            'Nama Angkut',
            'No Container',
            'Size',
            'ETA',
            'TPS Asal',
            'Consolidator',
            'No HBL',
            'Tgl HBL',
            'Customer',
            'Quantity',
            'Kode Kemas',
            'Weight',
            'Meas',
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
            'Tgl Release',
            'Jam Release',
            'Kode Dokumen',
            'Nomor Dokumen',
            'Tgl Dokumen',
            'Location',
            'Keterangan',
            'Lama Timbun',
        ];
    }
}
