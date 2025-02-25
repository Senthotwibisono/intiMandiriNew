<?php

namespace App\Imports\formatBaru;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\Manifest;
use App\Models\TempManifest;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Packing;
use App\Models\Pelabuhan;
use App\Models\Vessel;
use Auth;
use Carbon\Carbon;
use Str;

class dataManifest implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    protected $jobId;

    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }
    public function collection(Collection $rows)
    {
        // $sheetTitles = $import->getDelegate()->getActiveSheet()->getTitle();
        // dd($sheetTitles);
        // $headers = $rows->first()->keys()->toArray();
        // dd($headers); // This will output the column headers

        $firstRow = $rows->first();

        $pel_muat = trim($firstRow['kode_pelabuhan_asal']);
        $pel_transit = trim($firstRow['kode_pelabuhan_transit']);
        $pel_bongkar = trim($firstRow['kode_pelabuhan_bongkar']);

        $PM = Pelabuhan::where('kode', $pel_muat)->first();
        if ($PM) {
            $muat = $PM;
        }else {
            $muat = Pelabuhan::create([
                'kode' => $pel_muat
            ]);
        }

        $PT = Pelabuhan::where('kode', $pel_transit)->first();
        if ($PT) {
            $transit = $PT;
        }else {
            $transit = Pelabuhan::create([
                'kode' => $pel_transit
            ]);
        }

        $PB = Pelabuhan::where('kode', $pel_bongkar)->first();
        if ($PB) {
            $bongkar = $PB;
        }else {
            $bongkar = Pelabuhan::create([
                'kode' => $pel_bongkar
            ]);
        }

        $jobId = $this->jobId;
        $job = Job::find($jobId);
        $job->update([
            'pel_muat' => $muat->id,
            'pel_transit' => $transit->id,
            'pel_bongkar' => $bongkar->id,
        ]);

      
        foreach ($rows as $row) {

            $detil_id = trim($row['id_bl']);
            $nohbl = trim($row['nomor_host_bl']);
            $tgl_hbl = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_host_bl'])->format('Y-m-d');
            // dd($tgl_hbl);
            $npwp_consignee = trim($row['npwp_penerima']);
            $nama_consignee = trim($row['nama_penerima']);
            $almt_consignee = trim($row['alamat_penerima']);
            $npwp_shipper = trim($row['npwp_pengirim']);
            $nama_shipper = trim($row['nama_pengirim']);
            $almt_shipper = trim($row['alamat_pengirim']);
         
            $nama_notify = trim($row['nama_notify']);
            $almt_notify = trim($row['alamat_notify']);
            $npwp_notify = trim($row['npwp_notify']);
            $quantity = trim($row['jumlah_kemasan']);
            $jenis_kemasan = trim($row['jenis_kemasan']);
            $merk_kemasan = trim($row['marking']);
            $weight = trim($row['berat']);
            $meas = trim($row['dimensi']);
            // Hanya buat entri baru jika nama tidak kosong

            $oldCST = Customer::where('name', $nama_consignee)->where('npwp', $npwp_consignee)->first();
            if ($oldCST) {
                $customer = $oldCST;
            }else {
                $customer = Customer::create([
                    'name' => $nama_consignee,
                    'npwp' => $npwp_consignee,
                    'alamat' => $almt_consignee,
                ]);
            }

            $oldShipper = Customer::where('name', $nama_shipper)->where('npwp', $npwp_shipper)->first();
            if ($oldShipper) {
                $shipper = $oldShipper;
            }else {
                $shipper = Customer::create([
                    'name' => $nama_shipper,
                    'npwp' => $npwp_shipper,
                    'alamat' => $almt_shipper,
                ]);
            
            }

            if ($nama_notify == 'SAME AS CONSIGNEE') {
                $notify = $customer;
            }else {
                $oldNotify = Customer::where('name', $nama_shipper)->where('npwp', $npwp_shipper)->first();
                if ($oldNotify) {
                    $notify = $oldNotify;
                }else {
                    $notify = Customer::create([
                        'name' => $nama_notify,
                        'npwp' => $npwp_notify,
                        'alamat' => $almt_notify,
                    ]);
                }
            }

            $oldPack = Packing::where('code', $jenis_kemasan)->first();
            if ($oldPack) {
                $pack = $oldPack;
            }else {
                $pack = Packing::create([
                    'code' =>$jenis_kemasan,
                ]);
            }

            $manifest = TempManifest::create([
                'detil_id' => $detil_id,
                'nohbl' => $nohbl,
                'tgl_hbl' => $tgl_hbl,
                'shipper_id' => $shipper->id,
                'customer_id' => $customer->id,
                'notifyparty_id' => $notify->id,
                'marking' => $merk_kemasan,
                'quantity' => $quantity,
                'packing_id' => $pack->id,
                'weight' => $weight,
                'meas' => $meas,
            ]);
          
        }
    }
}
