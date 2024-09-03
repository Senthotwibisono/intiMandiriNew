<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Customer;

class CustomerExcel implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Ambil nilai dari setiap kolom dan bersihkan data
            $name = trim($row['name']);
            $email = trim($row['email']);
            $alamat = trim($row['alamat']);
            $code = trim($row['code']);
            $fax = trim($row['fax']);
            $phone = trim($row['phone']);
            $npwp = trim($row['npwp']);

            // Hanya buat entri baru jika nama tidak kosong
            if (!empty($name)) {
                // Cek apakah entri sudah ada
                $customer = Customer::where('name', $name)
                    ->where('email', $email)
                    ->where('alamat', $alamat)
                    ->where('code', $code)
                    ->where('fax', $fax)
                    ->where('phone', $phone)
                    ->where('npwp', $npwp)
                    ->first();
                
                // Jika entri belum ada, buat entri baru
                if (!$customer) {
                    $customer = new Customer();
                    $customer->name = $name;
                    $customer->email = $email;
                    $customer->alamat = $alamat;
                    $customer->code = $code;
                    $customer->fax = $fax;
                    $customer->phone = $phone;
                    $customer->npwp = $npwp;
                    $customer->save();
                }
            }
        }
    }
}

