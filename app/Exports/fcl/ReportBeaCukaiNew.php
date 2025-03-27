<?php

namespace App\Exports\fcl;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ReportBeaCukaiNew implements FromCollection, WithHeadings, WithStyles, WithMapping
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
                 'no_urut', 
                 'tps_code', 
                 'persetujuan_plp_no',
                 'persetujuan_plp_tgl', 
                 'contianer_no',
                 'contianer_uk',
                 'bc11_no',
                 'bc11_tgl',
                 'hbl_no',
                 'hbl_tgl',
                 'consignee',
                 'gate_in_tgl',
                 'gate_in_jam',
                 'gate_out_tgl',
                 'gate_out_tgl',
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
         foreach (range('A', 'V') as $column) {
             $sheet->getStyle($column)->getAlignment()->setWrapText(true); // Auto WrapText
             $sheet->getColumnDimension($column)->setAutoSize(false); // Hindari auto resize kolom
             $sheet->getColumnDimension($column)->setWidth(20); // Set width default 20
         }
 
         // Style untuk header (row 3 dan 4)
         $headerStyle = [
             'font' => ['bold' => true, 'color' => ['rgb' => '00000']], // Tulisan putih
             'fill' => [
                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                 'startColor' => ['rgb' => 'A7E6A7'] // Warna hijau
             ],
             'alignment' => [
                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
             ],
             'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'], // Warna hitam
                ],
            ],
         ];
 
         $sheet->getStyle('A1:V1')->applyFromArray($headerStyle);
 
         return [
             1 => ['alignment' => ['horizontal' => 'center']],
         ];
     }

    public function map($cont): array
    {
        

        static $index = 0; // Inisialisasi nomor urut
        $index++; // Tambah nomor urut setiap iterasi
        return [
            $index,
            '1MUT',
            $cont->job->noplp ?? '-',
            $cont->job->ttgl_plp ?? '-',
            $cont->nocontainer ?? '-',
            $cont->size ?? '-',
            $cont->job->tno_bc11 ?? '-',
            $cont->job->ttgl_bc11 ?? '-',
            $cont->nobl ?? '-',
            $cont->tgl_bl_awb ?? '-',
            $cont->customer->name ?? '-',
            $cont->tglmasuk ?? '-',
            $cont->jammasuk ?? '-',
            $cont->tglkeluar ?? '-',
            $cont->jamkeluar ?? '-',
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
