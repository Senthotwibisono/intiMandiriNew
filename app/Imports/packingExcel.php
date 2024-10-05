<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Packing;
use Auth;

class packingExcel implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Ambil nilai dari setiap kolom dan bersihkan data
            $code = trim($row['kode']);
            $name = trim($row['name']);
            $uid = Auth::user()->name;

            // Hanya buat entri baru jika nama tidak kosong
            if (!empty($name)) {
                // Cek apakah entri sudah ada
                $codeCheck = Packing::where('code', $code)->first();
                $nameCheck = Packing::where('name', $name)->first();
             
                if (!$nameCheck || !$codeCheck) {
                    $packing = new Packing();
                    $packing->code = $code;
                    $packing->name = $name;
                    $packing->uid = $uid;
                    $packing->save();
                }
            }
        }
    }
}

