<?php

namespace App\Exports\fcl;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;

use App\Models\ContainerFCL as Contf;

class ReportBulanan implements FromCollection, WithHeadings, WithStyles, WithMapping
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
             [$this->judul], // Judul utama (Merge A1:J1)
             [],
             [
                 'No. Urut', 
                 'Persetujuan PLP', '', 
                 'Peti Kemas', '', 
                 'BC 1.1', '', 
                 'Bill Of Lading', '', 
                 'Consignee', 
                 'SPPB (Jika sudah rilis)',
                 'Kode Dokumen',
                 'Jenis Dokumen'
             ], // Header utama (row 3)
             [
                 '', 
                 'Nomor', 'Tanggal', 
                 'Nomor', 'Ukuran', 
                 'Nomor', 'Tanggal', 
                 'Nomor', 'Tanggal', 
                 '', '', '', ''
             ] // Sub-header (row 4)
         ];
     }
 
     public function styles(Worksheet $sheet)
    {
        // Merge judul utama
        $sheet->mergeCells('A1:N1');
        $sheet->mergeCells('A3:A4'); // No. Urut
        $sheet->mergeCells('B3:C3'); // Persetujuan PLP
        $sheet->mergeCells('D3:E3'); // Peti Kemas
        $sheet->mergeCells('F3:G3'); // BC 1.1
        $sheet->mergeCells('H3:I3'); // Bill Of Lading
        $sheet->mergeCells('J3:J4'); // Consignee
        $sheet->mergeCells('K3:K4'); // SPPB
        $sheet->mergeCells('L3:L4'); // Kode Dokumen
        $sheet->mergeCells('M3:M4'); // Jenis Dokumen

        foreach (range('A', 'N') as $column) {
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
                'startColor' => ['rgb' => '4CAF50'] // Warna hijau
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];

        $sheet->getStyle('A3:M4')->applyFromArray($headerStyle);

        return [
            3 => ['alignment' => ['horizontal' => 'center']],
            4 => ['alignment' => ['horizontal' => 'center']],
        ];
    }
 
    public function map($cont): array
    {
        $tglPLP = $cont->job->ttgl_plp ?  Carbon::parse($cont->job->ttgl_plp)->format('d-m-Y') : '-';
        $tglBC11 = $cont->job->ttgl_bc11 ?  Carbon::parse($cont->job->ttgl_bc11)->format('d-m-Y') : '-';
        $tglmasuk = $cont->tglmasuk ? Carbon::parse($cont->tglmasuk)->format('d-m-Y') : 'Belum Masuk';
        $tglblAWB = $cont->tgl_bl_awb ? Carbon::parse($cont->tgl_bl_awb)->format('d-m-Y') : '-';
        $tglDok = $cont->tgl_dok ? Carbon::parse($cont->tgl_dok)->format('d-m-Y') : '-';

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
           $cont->nocontainer ?? '-',
           $cont->size ?? '-',
           $cont->job->tno_bc11 ?? '-',
           $tglBC11,
           $cont->nobl,
           $tglblAWB,
           $cont->Customer->name ?? '-',
           $cont->no_dok ?? '-',
           $cont->kd_dok_inout ?? '-',
           $cont->dokumen->name ?? '-',
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

    /**
     * Mengambil header otomatis berdasarkan struktur tabel
     */
}
