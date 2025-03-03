<?php

namespace App\Exports\lcl;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ReportContJICT implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $conts;
    protected $judul;

    public function __construct($conts, $judul)
    {
        $this->conts = $conts;
        $this->judul = $judul;
    }

    /**
     * Mengambil semua data berdasarkan joborder_id
     */

     public function collection()
     {
         return $this->conts;
     }

    

    public function headings(): array
    {
        return [
            ['LAPORAN REKAPITULASI PERMOHONAN PEMINDAHAN LOKASI PENIMBUNAN (PLP)'],
            ['TPS INTI MANDIRI UTAMA TRANS'],
            [$this->judul], // Judul utama (Merge A1:J1)
            [
                'No.',
                'NO Persetujuan PLP', 
                'Tgl Persetujuan PLP', 
                'Alasan PLP', 
                'Tujuan TPS', 
                'Nomor Peti Kemas', 
                'Size Cont.', 
                'No_BC_11',
                'Tgl_BC_11',
                'No_Pengajuan',
                'Tgl_Pengajuan',
                'Tgl_Keluar(gate out)',
                'Batal (Ya/Tidak)',
                'No_Pembatalan',
                'Tgl_Pembatalan',
                'Alasan_Pembatalan',
                'Keterangan',
            ], // Header utama (row 3)
            [
                '1',
                '2', 
                '3', 
                '4', 
                '5', 
                '6', 
                '7.', 
                '8',
                '9',
                '10',
                '11',
                '12',
                '13',
                '14',
                '15',
                '16',
                '17',
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge judul utama
        $sheet->mergeCells('A1:Q1');
        $sheet->mergeCells('A2:Q2');
        $sheet->mergeCells('A3:Q3');

        foreach (range('A', 'Q') as $column) {
            $sheet->getStyle($column)->getAlignment()->setWrapText(true); // Auto WrapText
            $sheet->getColumnDimension($column)->setAutoSize(false); // Hindari auto resize kolom
            $sheet->getColumnDimension($column)->setWidth(20); // Set width default 20
        }

        // Style untuk judul utama
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ]);

        // Style untuk header (row 3 dan 4)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], // Tulisan putih
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'A7C7E7'] // Warna hijau
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];

        $sheet->getStyle('A4:Q5')->applyFromArray($headerStyle);

        return [
            2 => ['alignment' => ['horizontal' => 'center']],
            3 => ['alignment' => ['horizontal' => 'center']],
            4 => ['alignment' => ['horizontal' => 'center']],
        ];
    }

    public function map($cont): array
    {
        $tglPLP = $cont->job->ttgl_plp ?  Carbon::parse($cont->job->ttgl_plp)->format('Y-m-d') : '-';
        $tglSurat = $cont->job->PLP->tgl_surat ?  Carbon::parse($cont->job->PLP->tgl_surat)->format('Y-m-d') : '-';
        $tglBC11 = $cont->job->ttgl_bc11 ?  Carbon::parse($cont->job->ttgl_bc11)->format('Y-m-d') : '-';
        $tglmasuk = $cont->tglmasuk ? Carbon::parse($cont->tglmasuk)->format('Y-m-d') : 'Belum Masuk';
        $tglblAWB = $cont->tgl_bl_awb ? Carbon::parse($cont->tgl_bl_awb)->format('Y-m-d') : '-';
        $tglDok = $cont->tgl_dok ? Carbon::parse($cont->tgl_dok)->format('Y-m-d') : '-';

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



        static $index = 0; // Inisialisasi nomor urut
        $index++; // Tambah nomor urut setiap iterasi
        return [
            $index,
            $cont->job->noplp ?? '-',
            $tglPLP,
            '2',
            '1MUT',
            $cont->nocontainer ?? '-',
            $cont->size ?? '-',
            $cont->job->tno_bc11 ?? '-',
            $tglBC11,
            $cont->job->plp->no_surat ?? '-',
            $tglSurat,
            $cont->tglkeluar ?? 'Belum Keluar',
            'TIDAK',
            'TIDAK',
            'TIDAK',
            'TIDAK',
            ' ',
            // $cont->nobl,
            // $tglblAWB,
            // $cont->Customer->name ?? '-',
            // $cont->no_dok ?? '-',
            // $cont->kd_dok_inout ?? '-',
            // $cont->dokumen->name ?? '-',
        //    '-',
        //    $cont->ctr_type ?? '-',
        //    $cont->dokumen->name ?? '-',
        //    $cont->job->sandar->kd_tps_asal ?? '-',
        //    '-',
        //    '-',
        //    $tglmasuk,
        //    $cont->jammasuk ?? '-',
        //    $cont->nopol ?? '-',
        //    $lamaHari,
        //    $longStay,
        //    $kapalVoy,
        //    $cont->job->eta ?? '-',
        //    $cont->tglkeluar ?? 'Belum Keluar',
        //    $cont->jamkeluar ?? 'Belum Keluar',
        //    $cont->nopol_mty ?? '-',
        ];
    }
}
