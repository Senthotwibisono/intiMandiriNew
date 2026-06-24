<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\FCL\ReportOpnameController;

class KirimLaporanSppb extends Command
{
    protected $signature = 'sppb:otomatis';
    protected $description = 'Kirim laporan SPPB belum gateout';

    public function handle()
    {
        $controller = new ReportOpnameController(); 
    
        try {
            $controller->sppbOtomatis();
            $this->info('send SPBB otomatis executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in send SPBB otomatis: ' . $e->getMessage());
        }
    }
}