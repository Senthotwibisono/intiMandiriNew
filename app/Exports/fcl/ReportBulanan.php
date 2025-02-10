<?php

namespace App\Exports\fcl;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Carbon\Carbon;

use App\Models\ContainerFCL as Contf;

class ReportBulanan implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $conts;

    public function __construct($conts)
    {
        $this->conts = $conts;
    }

    /**
     * Mengambil semua data berdasarkan joborder_id
     */

     public function collection()
     {
         return $this->conts;
     }
    public function map($cont): array
    {
        $tglPLP = $cont->job->ttgl_plp ?  Carbon::parse($cont->job->ttgl_plp)->format('d-m-Y') : '-';
        $tglBC11 = $cont->job->ttgl_bc11 ?  Carbon::parse($cont->job->ttgl_bc11)->format('d-m-Y') : '-';
        $tglmasuk = $cont->tglmasuk ? Carbon::parse($cont->tglmasuk)->format('d-m-Y') : 'Belum Masuk';

        $kapal = $cont->job->Kapal->name ?? '-';
        $voy = $cont->job->voy ?? '-';
        $kapalVoy = $kapal.'/'.$voy;

        if (!$cont->tglmasuk) {
            $lamaHari = 'Belum Masuk';
            $longStay = 'N';
        } else {
            $lamaHari = Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglkeluar ?? now()) . ' hari';

            if (Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglkeluar ?? now()) >= 25 ) {
                $longStay = 'Y';
            }else {
                $longStay = 'N';
            }
        }
        return [
           $cont->job->noplp ?? '-',
           $tglPLP,
           $cont->job->tno_bc11 ?? '-',
           $tglBC11,
           $cont->Customer->name ?? '-',
           $cont->nocontainer ?? '-',
           '-',
           $cont->ctr_type ?? '-',
           $cont->dokumen->name ?? '-',
           $cont->size ?? '-',
           $cont->job->sandar->kd_tps_asal ?? '-',
           '-',
           '-',
           $tglmasuk,
           $cont->jammasuk ?? '-',
           $cont->nopol ?? '-',
           $lamaHari,
           $longStay,
           $kapalVoy,
           $cont->job->eta ?? '-',
           $cont->tglkeluar ?? 'Belum Keluar',
           $cont->jamkeluar ?? 'Belum Keluar',
           $cont->nopol_mty ?? '-',
        ];
    }

    /**
     * Mengambil header otomatis berdasarkan struktur tabel
     */
    public function headings(): array
    {
        return [
           'PLP',
           'TGL PLP',
           'BC 1.1',
           'TGL BC 1.1',
           'Consigne',
           'No Container',
           'Keterangan Batal',
           'Jenis Container',
           'Jenis Dok',
           'Size',
           'Kd TPS Asal',
           'Out TPS Asal (Tanggal)',
           'Out TPS Asal (Jam)',
           'Tgl Masuk',
           'Jam Masuk',
           'No Polisi Masuk',
           'Lama Hari',
           'Status Normal/Longstay',
           'Nama Kapal',
           'Tgl Tiba Kapal',
           'Tgl Keluar TPS',
           'Jam Keluar TPS',
           'No Polisi Keluar',
        ];
    }
}
