<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\PPJK;
use Auth;
use Carbon\Carbon;

class ppjkExcel implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Ambil nilai dari setiap kolom dan bersihkan data
            $code = trim($row['phone']);
            $name = trim($row['name']);
            $uid = Auth::user()->name;
            $now = Carbon::now();

            // Hanya buat entri baru jika nama tidak kosong
            if (!empty($name)) {
                // Cek apakah entri sudah ada
                $nameCheck = PPJK::where('name', $name)->first();
             
                if (!$nameCheck || !$codeCheck) {
                    $ppjk = new PPJK();
                    $ppjk->phone = $phone;
                    $ppjk->name = $name;
                    $ppjk->uid = $uid;
                    $ppjk->created_at = $now;
                    $ppjk->save();
                }
            }
        }
    }
}

