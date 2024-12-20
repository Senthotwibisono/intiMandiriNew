<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TestJson as TJS;

class TestJsonController extends Controller
{
    public function test()
    {
        $data = []; // Inisialisasi array kosong
        $names = TJS::all(); // Ambil semua data dari model TJS

        foreach ($names as $name) {
            $data[$name->name] = [];
            $types = $names->where('name', $name->name)->unique('type');

            foreach ($types as $type) {
                $data[$name->name][$type->type] = []; 

                $keteranganData = $names->where('name', $name->name)
                    ->where('type', $type->type);

                foreach ($keteranganData as $item) {
                    $data[$name->name][$type->type][$item->keterangan][] = $item->desk;
                }
            }
        }

        // Debug data

       
        return $data;
    }


}
