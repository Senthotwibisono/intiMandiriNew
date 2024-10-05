<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Perusahaan;
use Auth;

class perusahaanExcel implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Ambil nilai dari setiap kolom dan bersihkan data
            $name = trim($row['name']);
            $alamat = trim($row['alamat']);
            $kota = trim($row['kota']);
            $phone = trim($row['phone']);
            $fax = trim($row['fax']);
            $email = trim($row['email']);
            $cp = trim($row['cp']);
            $roles = trim($row['roles']);
            $npwp = trim($row['npwp']);
            $ppn = trim($row['ppn']);
            $materai = trim($row['materai']);
            $nppkp = trim($row['nppkp']);
            
            $uid = Auth::user()->id;

            // Hanya buat entri baru jika nama tidak kosong
            if (!empty($name)) {
                // Cek apakah entri sudah ada
                $perusahaan = Perusahaan::where('name', $name)->first();
             
                if (!$perusahaan) {
                    $perusahaan = new Eseal();
                    $perusahaan->name = $name;
                    $perusahaan->alamat = $alamat;
                    $perusahaan->kota = $kota;
                    $perusahaan->phone = $phone;
                    $perusahaan->fax = $fax;
                    $perusahaan->email = $email;
                    $perusahaan->cp = $cp;
                    $perusahaan->roles = $roles;
                    $perusahaan->uid = $uid;
                    $perusahaan->npwp = $npwp;
                    $perusahaan->ppn = $ppn;
                    $perusahaan->materai = $materai;
                    $perusahaan->nppkp = $nppkp;
                    $perusahaan->save();
                }
            }
        }
    }
}

