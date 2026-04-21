<?php

namespace App\Exports\lcl;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\TpsPLPdetail;

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
            'NO',
            'KODE TPS',
            'TANGGAL TIMBUN',
            'BC11_NO',
            'BC11_TANGGAL',
            'BC11_POS',
            'SHIPMENT',
            'CONT_JENIS',
            'CONT_NO',
            'E / I',
            'SIZE',
            'HBL_NO',
            'HBL_TANGGAL',
            'KAPAL',
            'VOYAGE',
            'PELAYARAN',
            'ALAMAT PELAYARAN',
            'JML_KEMASAN',
            'JENIS_KMS',
            'URAIAN BARANG',
            'CONSIGNEE',
            'POSISI',
            'DOKUMEN_JENIS',
            'DOKUMEN_NOMOR',
            'DOKUMEN_TANGGAL',
            'GATEOUT_TANGGAL',
            'CTP_NO',
            'CTP_TANGGAL',
            'NHI_STATUS',
            'SPBL_NO',
            'SPBL_TANGGAL',
            'KAHAR',
            'KETERANGAN',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $headers = $this->headings();
        $totalColumn = count($headers); 

        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumn);   

        for ($i = 1; $i <= $totalColumn; $i++) {

            $column = Coordinate::stringFromColumnIndex($i);

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
        $plpId = optional($cont->job->PLP)->id;

        $bc11_pos = $plpId
            ? TpsPLPdetail::where('plp_id', $plpId)
                ->where('no_bl_awb', $cont->nohbl)
                ->value('no_pos_bc11')
            : '-';

        return [
            $index,
            '1MUT',
            $cont->tglstripping ?? '',
            // $cont->job->noplp ?? '-',
            // $cont->job->ttgl_plp ?? '-',
            $cont->job->tno_bc11 ?? '-',
            $cont->job->ttgl_bc11 ?? '-',
            $bc11_pos ?? '-',
            '',

            $cont->cont->ctr_type ?? 'DRY',
            $cont->cont->nocontainer ?? '-',
            'I',
            $cont->cont->size ?? '-',
            $cont->nohbl ?? '-',
            $cont->tgl_hbl ?? '-',
            $cont->job->Kapal->name ?? '-',
            $cont->job->voy ?? '-',
            '',
            '',
            $cont->quantity ?? '',
            $cont->packing->code ?? '',
            $cont->descofgoods ?? '',
            $cont->customer->name ?? '-',


            $cont->allItemsLocations()->implode(', ') ?? '',
            // $cont->jammasuk ?? '-',
            // $cont->jamkeluar ?? '-',
            $cont->dokumen->name ?? '-',
            $cont->no_dok ?? '-',
            $cont->tgl_dok ?? '-',
            $cont->tglrelese ?? '-',
            $cont->nosegel ?? '-',
            $cont->tanggal_segel_merah ? Carbon::parse($cont->tanggal_segel_merah)->format('Y-m-d') : '-',
            '-',
            '-',
        ];
    }
}
