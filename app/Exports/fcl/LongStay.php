<?php

namespace App\Exports\fcl;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class LongStay implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $conts;
    protected $judul;

    public function __construct($conts)
    {
        $this->conts = $conts;
    }

    public function collection()
    {
        return $this->conts;
    }

    public function headings(): array
     {
         return [
                 'no_urut', 
                 'KODE TPS',
                 'TANGGAL TIMBUN',
                //  'persetujuan_plp_no',
                //  'persetujuan_plp_tgl', 
                 'BC11_NO',
                 'BC11_TANGGAL',
                 'CONT_JENIS',
                 'CONT_NO',
                 'SIZE',
                 'hbl_no',
                 'hbl_tgl',
                 'KAPAL',
                 'VOYAGE',
                 'consignee',
                //  'gate_in_tgl',
                //  'gate_in_jam',
                 'gate_out_tgl',
                //  'gate_out_tgl',
                 'dok_penyelesaian_jenis',
                 'dok_penyelesaian_no',
                 'dok_penyelesaian_tgl',
                 'ctp_no',
                 'ctp_tgl',
                 'spbl_no',
                 'spbl_tgl',
                 
         ]; // Header utama (row 3)
     }

    public function styles(Worksheet $sheet)
    {
        $headers = $this->headings();
        $totalColumn = count($headers); 

        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumn);   

        foreach (range('A', $lastColumn) as $column) {
            $sheet->getStyle($column)
                ->getAlignment()
                ->setWrapText(true);    

            $sheet->getColumnDimension($column)->setAutoSize(false);
            $sheet->getColumnDimension($column)->setWidth(20);
        }   

        // HEADER STYLE (ABU-ABU)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // hitam
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9'], // abu-abu Excel standard
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];  

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray($headerStyle);    

        return [
            1 => [
                'alignment' => [
                    'horizontal' => 'center',
                ],
            ],
        ];
    }

    public function map($cont): array
    {
        

        static $index = 0; // Inisialisasi nomor urut
        $index++; // Tambah nomor urut setiap iterasi
        return [
            $index,
            '1MUT',
            $cont->tglmasuk ?? '-',
            // $cont->job->noplp ?? '-',
            // $cont->job->ttgl_plp ?? '-',
            $cont->job->tno_bc11 ?? '-',
            $cont->job->ttgl_bc11 ?? '-',
            $cont->ctr_type ?? '-',
            $cont->nocontainer ?? '-',
            $cont->size ?? '-',
            $cont->nobl ?? '-',
            $cont->tgl_bl_awb ?? '-',
            $cont->job->Kapal->name ?? '-',
            $cont->job->voy ?? '-',
            $cont->customer->name ?? '-',
            // $cont->jammasuk ?? '-',
            $cont->tglkeluar ?? '-',
            // $cont->jamkeluar ?? '-',
            $cont->dokumen->name ?? '-',
            $cont->no_dok ?? '-',
            $cont->tgl_dok ?? '-',
            $cont->nosegel ?? '-',
            $cont->tanggal_segel_merah ? Carbon::parse($cont->tanggal_segel_merah)->format('Y-m-d') : '-',
            '-',
            '-',
        ];
    }
}
