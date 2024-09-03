<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Eseal;
use Auth;

class EsealExcel implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Ambil nilai dari setiap kolom dan bersihkan data
            $code = trim($row['kode']);
            $keterangan = trim($row['keterangan']);
            $uid = Auth::user()->id;

            // Hanya buat entri baru jika nama tidak kosong
            if (!empty($code)) {
                // Cek apakah entri sudah ada
                $eseal = Eseal::where('code', $code)->first();
             
                if (!$eseal) {
                    $eseal = new Eseal();
                    $eseal->code = $code;
                    $eseal->keterangan = $keterangan;
                    $eseal->uid = $uid;
                    $eseal->save();
                }
            }
        }
    }
}

