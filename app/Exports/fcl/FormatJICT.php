<?php

namespace App\Exports\fcl;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use Illuminate\Support\Collection;
use Carbon\Carbon;

use App\Models\ContainerFCL as Contf;

class FormatJICT implements FromCollection, WithHeadings, WithStyles, WithMapping
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
             ['-'],
             [$this->judul], // Judul utama (Merge A1:J1)
             [''],
             [
                 'No', 
                 'Container', 
                 'Ukuran', '', '', 
                 'TANGGAL OBX DARI JICT', 
                 'TANGGAL KELUAR DARI DEPO', 
                 'EX VESSEL', 
                 'ETA',
                 'CONSIGNEE',
                 'BL',
                 'SHIPPING LINE'
             ], // Header utama (row 3)
             [
                 '', 
                 '', 
                 '20', '40', '45', 
                 '', '', 
                 '', '', 
                 '', '', 
                 '', '', '', ''
             ] // Sub-header (row 4)
         ];
     }
 
    public function styles(Worksheet $sheet)
    {
        // Merge judul utama
        $sheet->mergeCells('A2:N2');
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:E4'); 
        $sheet->mergeCells('F4:F5');  
        $sheet->mergeCells('G4:G5');  
        $sheet->mergeCells('H4:H5'); 
        $sheet->mergeCells('I4:I5'); 
        $sheet->mergeCells('J4:J5'); 
        $sheet->mergeCells('K4:K5'); 
        $sheet->mergeCells('L4:L5'); 

        foreach (range('A', 'L') as $column) {
            $sheet->getStyle($column)->getAlignment()->setWrapText(true); // Auto WrapText
            $sheet->getColumnDimension($column)->setAutoSize(false); // Hindari auto resize kolom
            $sheet->getColumnDimension($column)->setWidth(20); // Set width default 20
        }

        // Style untuk judul utama
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
        ]);

        // Style untuk header (row 3 dan 4)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']], // Tulisan putih
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00B3FF'] // Warna hijau
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];

        $highestRow = $sheet->getHighestRow(); // Ambil baris terakhir
        $sheet->getStyle('A4:L' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'], // Warna hitam
                ],
            ],
        ]);

        $sheet->getStyle('A4:l5')->applyFromArray($headerStyle);

        return [
            4 => ['alignment' => ['horizontal' => 'center']],
            5 => ['alignment' => ['horizontal' => 'center']],
        ];
    }
 
    public function map($cont): array
    {
        static $index = 0; // Nomor urut untuk data kontainer
        $index++; // Mulai dari 1 setelah header
        $kapal = $cont->job->Kapal->name ?? '-';
        $voy = $cont->job->voy ?? '-';
        $kapalVoy = $kapal.'/'.$voy;
        $masuk = ($cont->tglmasuk) ? Carbon::parse($cont->tglmasuk)->translatedFormat('d F Y') : 'Belum Masuk';
        $keluar = ($cont->tglkeluar) ? Carbon::parse($cont->tglkeluar)->translatedFormat('d F Y') : 'Belum Keluar';
        $eta = ($cont->job->eta) ? Carbon::parse($cont->job->eta)->translatedFormat('d F Y') : 'Tidak ditemukan';
        $cont20 = ($cont->size == '20') ? 1 : '-';
        $cont40 = ($cont->size == '40') ? 1 : '-';
        $cont45 = ($cont->size == '45') ? 1 : '-';
    
        return [
           $index, // Nomor urut dimulai setelah header
           $cont->nocontainer ?? '-',
           $cont20,
           $cont40,
           $cont45,
           $masuk ?? '-',
           $keluar ?? '-',
           $kapalVoy ?? '-',
           $eta ?? '-',
           $cont->Customer->name ?? '-',
           $cont->nobl ?? '-',
           $cont->job->shipping->shipping_line ?? '-',
        ];
    }
    

    /**
     * Mengambil header otomatis berdasarkan struktur tabel
     */
}
