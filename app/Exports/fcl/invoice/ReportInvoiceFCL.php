<?php

namespace App\Exports\fcl\invoice;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\ContainerFCL as ContF;
use App\Models\JobOrderFCL as JobF;
use App\Models\FCL\FormContainerFCL as FormC;
use App\Models\FCL\FormFCL as Form;
use App\Models\FCL\MTarifTPS as TTPS;
use App\Models\FCL\MTarifWMS as TWMS;
use App\Models\FCL\InvoiceHeader as Header;
use App\Models\FCL\InvoiceDetil as Detil;
use App\Models\FCL\CanceledInvoice as InvCancel;
class ReportInvoiceFCL implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $headers;
    protected $judul;

    public function __construct($headers, $judul)
    {
        $this->headers = $headers;
        $this->judul = $judul;
    }

    /**
     * Mengambil semua data berdasarkan joborder_id
     */

     public function collection()
     {
         return $this->headers;
     }

     public function headings(): array
     {
         return [
             [$this->judul], // Judul utama (Merge A1:J1)
             [],
             [
                 'No. Urut', 
                 'No. Invoice', 
                 'Tgl Invoice', 
                 'Consignee', '', '', '', 
                 'Container', '', '', '',
                 'Harga', '', '', '',
                 'Pembayaran', '',
                 'Keterangan',
                 'Kontak',
                 'URL'
             ], // Header utama (row 3)
             [
                 '', 
                 '', 
                 '', 
                 'Nama', 'NPWP', 'Alamat', 'Fax',
                 '20', '40', '45', 'KD TPS',
                 'DPP', 'TAX', 'Materai', 'Grand Total', 
                 'Jumlah Di Bayarkan', 'selisih',
                 '',
                 ''
             ] // Sub-header (row 4)
         ];
     }

     public function styles(Worksheet $sheet)
     {
         // Merge Cells
         $sheet->mergeCells('A1:N1'); // Judul
         $sheet->mergeCells('A3:A4'); // No urut
         $sheet->mergeCells('B3:B4'); // No Invoice
         $sheet->mergeCells('C3:C4'); // Tgl Invoice
         $sheet->mergeCells('D3:G3'); // Consignee
         $sheet->mergeCells('H3:K3'); // Container
         $sheet->mergeCells('L3:O3'); // Harga
         $sheet->mergeCells('P3:Q3'); // Pembayaran
         $sheet->mergeCells('R3:R4'); // Keterangan
         $sheet->mergeCells('S3:S4'); // Kontak
         $sheet->mergeCells('T3:T4'); // URL
     
         // Apply wrap text to all cells with values
         $sheet->getStyle('A1:T100')->getAlignment()->setWrapText(true);  // Assuming data range is up to row 100
     
         // Style for the main title
         $sheet->getStyle('A1')->applyFromArray([
             'font' => ['bold' => true, 'size' => 14],
             'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
         ]);
     
         // Style for header (rows 3 and 4)
         $headerStyle = [
             'font' => ['bold' => true],
             'alignment' => [
                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
             ],
         ];
     
         $sheet->getStyle('A3:T4')->applyFromArray($headerStyle);
     
         // Border style (thin borders with black color)
         $borderStyle = [
             'borders' => [
                 'allBorders' => [
                     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,  // Thin borders
                     'color' => ['argb' => '000000'],  // Black border color
                 ],
             ],
         ];
     
         // Iterate through all rows and columns (A1:S100 as an example range)
         foreach ($sheet->getRowIterator(1, 100) as $row) {
             foreach ($row->getCellIterator('A', 'T') as $cell) {
                 // Apply border and wrap text only if the cell has a value
                 if ($cell->getValue() !== null && $cell->getValue() !== '') {
                     $cell->getStyle()->applyFromArray($borderStyle);  // Apply border
                     $cell->getStyle()->getAlignment()->setWrapText(true);  // Apply wrap text
                 }
             }
         }
     
         return [
             3 => ['alignment' => ['horizontal' => 'center']],
             4 => ['alignment' => ['horizontal' => 'center']],
         ];
     }
     


     public function map($header): array
    {
        static $index = 0; // Inisialisasi nomor urut
        $index++; // Tambah nomor urut setiap iterasi

        $tglInvoice = $header->created_at ? Carbon::parse($header->created_at)->format('d/m/Y') : '-';

        $cont20 = Detil::where('invoice_id', $header->id)->where('size', '20')->pluck('jumlah')->first() ?? 0;
        $cont40 = Detil::where('invoice_id', $header->id)->where('size', '40')->pluck('jumlah')->first() ?? 0;
        $cont45 = Detil::where('invoice_id', $header->id)->where('size', '45')->pluck('jumlah')->first() ?? 0;
        
        $keterangan = 'Pembayaran sudah sesuai';

        if ($header->grand_total > $header->jumlah_bayar) {
            $keterangan = 'Pembayaran Kurang sebesar: ' . number_format($header->sisa_bayar);
        } elseif ($header->grand_total < $header->jumlah_bayar) {
            $keterangan = 'Pembayaran Lebih sebesar: ' . number_format($header->sisa_bayar);
        }


        if ($header->grand_total >= 5000000) {
            $materai = number_format(10000);
        }else {
            $materai = 0;
        }

        if ($header->status == 'Y') {
            $url = 'https://inti-mandiri.com/invoiceFCL/invoice/invoice-' . $header->id;
        }elseif ($header->status == 'N') {
            $url = 'https://inti-mandiri.com/invoiceFCL/invoice/pranota-' . $header->id;
        }else {
            $url = 'Incoice Cnceled';
        }
        return [
            $index,
            $header->invoice_no ?? 'Belum Melakukan Pembayaran',
            $header->created_at,
            $header->cust_name ?? '',
            $header->cust_alamat ?? '',
            $header->cust_npwp ?? '',
            $header->cust_fax ?? '',
            $cont20,
            $cont40,
            $cont45,
            $header->kd_tps_asal,
            number_format($header->total, 0) ?? '0',
            number_format( $header->ppn, 0) ?? '0',
            $materai,
            number_format( $header->grand_total, 0) ?? '0',
            number_format( $header->jumlah_bayar, 0) ?? '0',
            number_format(abs($header->sisa_bayar), 0) ?? '0',
            $keterangan,
            $header->no_hp,
            $url,
        ];
    }
}
