<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\pengiriman\CoariCodecoController;
class CodecoCoari extends Command

{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'functions:daily';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new CoariCodecoController(); // Ganti dengan nama controller Anda

        // Panggil semua function
        $controller->coariCont(); // Ganti dengan nama function pertama
        $controller->CoariKms(); // Ganti dengan nama function kedua
        $controller->CodecoCont(); // Ganti dengan nama function ketiga
        $controller->CodecoKms(); // Ganti dengan nama function keempat

        $this->info('All daily functions have been executed successfully!');
    }
}
