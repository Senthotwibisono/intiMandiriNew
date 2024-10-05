<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Consolidator;
use Auth;
use Carbon\Carbon;
class consolidatorExcel implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Ambil nilai dari setiap kolom dan bersihkan data
            $namaconsolidator = trim($row['namaconsolidator']);
            $notelp = trim($row['notelp']);
            $contactperson = trim($row['contactperson']);
            $nocano = trim($row['nocano']);
            $tglakhirkontrak = Carbon::createFromFormat('d/m/Y', trim($row['tglakhirkontrak']))->format('Y-m-d');
            $kontrak = trim($row['kontrak']);
            $uid = Auth::user()->id;
            $npwp = trim($row['npwp']);
            $ppn = trim($row['ppn']);
            $materai = trim($row['materai']);
            $nppkp = trim($row['nppkp']);
            $keterangan = trim($row['keterangan']);
            


            // Hanya buat entri baru jika nama tidak kosong
            if (!empty($namaconsolidator)) {
                // Cek apakah entri sudah ada
                $consolidator = Consolidator::where('namaconsolidator', $namaconsolidator)
                    ->where('notelp', $notelp)
                    ->where('contactperson', $contactperson)
                    ->where('nocano', $nocano)
                    ->where('tglakhirkontrak', $tglakhirkontrak)
                    ->where('kontrak', $kontrak)
                    ->where('npwp', $npwp)
                    ->where('ppn', $ppn)
                    ->where('materai', $materai)
                    ->where('nppkp', $nppkp)
                    ->where('keterangan', $keterangan)
                    ->first();
                
                // Jika entri belum ada, buat entri baru
                if (!$consolidator) {
                    $consolidator = new Consolidator();
                    $consolidator->namaconsolidator = $namaconsolidator;
                    $consolidator->notelp = $notelp;
                    $consolidator->contactperson = $contactperson;
                    $consolidator->nocano = $nocano;
                    $consolidator->tglakhirkontrak = $tglakhirkontrak;
                    $consolidator->kontrak = $kontrak;
                    $consolidator->uid = $uid;
                    $consolidator->npwp = $npwp;
                    $consolidator->ppn = $ppn;
                    $consolidator->materai = $materai;
                    $consolidator->nppkp = $nppkp;
                    $consolidator->keterangan = $keterangan;
                    $consolidator->save();
                }
            }
        }
    }
}

