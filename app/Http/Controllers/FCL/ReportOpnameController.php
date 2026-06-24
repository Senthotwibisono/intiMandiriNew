<?php

namespace App\Http\Controllers\FCL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Auth;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use App\Mail\SppbBelumGateoutMail;

use App\Models\ContainerFCL as Cont;

class ReportOpnameController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = "Report Opname SPPB & SPJM";

        return view('fcl.report.opname', $data);
    }

    public function sppbData($tanggal)
    {
         // 2026-06-16
        $batasTanggal = $tanggal . ' 23:59:59';     

        $data = DB::table('tcontainer_fcl as c')
            ->leftJoin('tjoborder_fcl as j', 'j.id', '=', 'c.joborder_id')
            ->leftJoin('tvessel as v', 'v.id', '=', 'j.vessel')
            ->leftJoin('kode_dok as k', 'k.id', '=', 'c.kd_dok_inout')
            ->leftJoin('customer as cust', 'cust.id', '=', 'c.cust_id')     

            ->leftJoin('tps_sppbxml as sppb', function ($join) {
                $join->on('sppb.no_sppb', '=', 'c.no_dok')
                     ->where('c.kd_dok_inout', 1);
            })      

            ->leftJoin('tps_sppbbc23xml as bc23', function ($join) {
                $join->on('bc23.no_sppb', '=', 'c.no_dok')
                     ->where('c.kd_dok_inout', 2);
            })      

            ->select([
                DB::raw("'' as asal_barang"),
                'v.name as nama_kapal',
                'j.voy as no_voyage',
                'j.eta as tgl_tiba',
                'j.tno_bc11 as nobc11',
                'j.ttgl_bc11 as tglbc11',       

                'c.nocontainer as no_cont',
                'c.size as uk_cont',        

                'c.kd_dok_inout as kd_doc',
                'k.name as doc_name',       

                'c.no_dok as no_bc',
                'c.tgl_dok as tgl_bc',      

                DB::raw("
                    CASE
                        WHEN c.kd_dok_inout = 1 THEN sppb.no_pib
                        WHEN c.kd_dok_inout = 2 THEN bc23.no_pib
                    END as no_pib
                "),     

                DB::raw("
                    CASE
                        WHEN c.kd_dok_inout = 1 THEN sppb.tgl_pib
                        WHEN c.kd_dok_inout = 2 THEN bc23.tgl_pib
                    END as tgl_pib
                "),     

                'cust.name as nama_importir',
                'cust.npwp as npwp_importir',       

                DB::raw("'' as gate_out_date"),
            ])      

            ->whereNotNull('c.no_dok')
            ->where('c.no_dok', '<>', '')       

            // dokumen sudah terbit sampai tanggal laporan
            ->where('c.tgl_dok', '<=', $tanggal)       

            // masih berada di TPS pada akhir hari laporan
            ->where(function ($q) use ($batasTanggal) {
                $q->whereNull('c.tglkeluar')
                  ->orWhereRaw("
                        CONCAT(
                            c.tglkeluar,
                            ' ',
                            IFNULL(c.jamkeluar,'00:00:00')
                        ) > ?
                    ", [$batasTanggal]);
            })   

            ->orderBy('cust.name')
            ->orderBy('c.tgl_dok')
            ->get();

            return $data;
            // dd($data, $request->all());
    }

    public function sppb(Request $request)
    {
        $tanggal = $request->date;
        $batasTanggal = $tanggal . ' 23:59:59'; 

        $data = $this->sppbData($tanggal);// query Anda   
        // dd($data);

        $spreadsheet = IOFactory::load(
            storage_path('app/template/format laporan TPS - SPPB belum gateout.xlsx')
        );  

        $sheet = $spreadsheet->getActiveSheet();    

        // Nama TPS
        $sheet->setCellValue('B17', 'PT INTI MANDIRI UTAMA TRANS'); 

        $row = 20;
        $no = 1;    

        foreach ($data as $item) {  

            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $item->no_cont);
            $sheet->setCellValue('C'.$row, $item->uk_cont);
            $sheet->setCellValue('D'.$row, $item->nama_importir);
            $sheet->setCellValue('E'.$row, $item->npwp_importir);
            $sheet->setCellValue('F'.$row, $item->no_pib);  

            if($item->tgl_pib){
                $sheet->setCellValue(
                    'G'.$row,
                    Date::PHPToExcel(
                        \Carbon\Carbon::parse($item->tgl_pib)
                    )
                );
                $sheet->getStyle('G'.$row)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            }   

            $sheet->setCellValue('H'.$row, $item->doc_name);
            $sheet->setCellValue('I'.$row, $item->no_bc);   

            if($item->tgl_bc){
                $sheet->setCellValue(
                    'J'.$row,
                    Date::PHPToExcel(
                        \Carbon\Carbon::parse($item->tgl_bc)
                    )
                );
                $sheet->getStyle('J'.$row)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            }   

            $sheet->setCellValue('K'.$row, $item->nobc11);  

            if($item->tglbc11){
                $sheet->setCellValue(
                    'L'.$row,
                    Date::PHPToExcel(
                        \Carbon\Carbon::parse($item->tglbc11)
                    )
                );
                $sheet->getStyle('L'.$row)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            }   

            $row++;
        }   

        $filename = 'Laporan TPS - SPPB belum gateout '.date('d-m-Y', strtotime($tanggal)).'.xlsx'; 
        $tempFile = storage_path('app/temp/'.$filename);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFile);

        // Mail::to([
        //     'bcpriok.penindakan@gmail.com',
        //     'duktek.bcpriok@kemenkeu.go.id',
        //     'ppc2.bcpriok@kemenkeu.go.id',
        //     // 'fajrul.muflichin02@gmail.com',
        //     // 'azzambackup326@gmail.com'
        // ])->send(
        //     new SppbBelumGateoutMail(
        //         date('d-m-Y', strtotime($tanggal)),
        //         $tempFile
        //     )
        // );

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function sppbOtomatis()
    {
        $tanggal = Carbon::yesterday()->format('Y-m-d');
        $batasTanggal = $tanggal . ' 23:59:59'; 

        $data = $this->sppbData($tanggal);// query Anda   
        // dd($data);

        $spreadsheet = IOFactory::load(
            storage_path('app/template/format laporan TPS - SPPB belum gateout.xlsx')
        );  

        $sheet = $spreadsheet->getActiveSheet();    

        // Nama TPS
        $sheet->setCellValue('B17', 'PT INTI MANDIRI UTAMA TRANS'); 

        $row = 20;
        $no = 1;    

        foreach ($data as $item) {  

            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $item->no_cont);
            $sheet->setCellValue('C'.$row, $item->uk_cont);
            $sheet->setCellValue('D'.$row, $item->nama_importir);
            $sheet->setCellValue('E'.$row, $item->npwp_importir);
            $sheet->setCellValue('F'.$row, $item->no_pib);  

            if($item->tgl_pib){
                $sheet->setCellValue(
                    'G'.$row,
                    Date::PHPToExcel(
                        \Carbon\Carbon::parse($item->tgl_pib)
                    )
                );
                $sheet->getStyle('G'.$row)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            }   

            $sheet->setCellValue('H'.$row, $item->doc_name);
            $sheet->setCellValue('I'.$row, $item->no_bc);   

            if($item->tgl_bc){
                $sheet->setCellValue(
                    'J'.$row,
                    Date::PHPToExcel(
                        \Carbon\Carbon::parse($item->tgl_bc)
                    )
                );
                $sheet->getStyle('J'.$row)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            }   

            $sheet->setCellValue('K'.$row, $item->nobc11);  

            if($item->tglbc11){
                $sheet->setCellValue(
                    'L'.$row,
                    Date::PHPToExcel(
                        \Carbon\Carbon::parse($item->tglbc11)
                    )
                );
                $sheet->getStyle('L'.$row)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            }   

            $row++;
        }   

        $filename = 'Laporan TPS - SPPB belum gateout ' .
            date('d-m-Y', strtotime($tanggal)) .
            '_' .
            now()->format('His') .
            '.xlsx';        

        $tempDir = storage_path('app/temp');        

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }       

        if (!is_writable($tempDir)) {
            throw new \Exception("Folder tidak bisa ditulis: {$tempDir}");
        }       

        $tempFile = $tempDir . '/' . $filename;     

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFile);       

        Mail::to([
            'bcpriok.penindakan@gmail.com',
            'duktek.bcpriok@kemenkeu.go.id',
            'ppc2.bcpriok@kemenkeu.go.id',
            'fajrul.muflichin02@gmail.com',
            // 'azzambackup326@gmail.com'
        ])->send(
            new SppbBelumGateoutMail(
                date('d-m-Y', strtotime($tanggal)),
                $tempFile
            )
        );      

        // hapus file setelah email terkirim
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }       

        return true;
    }
}
