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
        $controller = new CoariCodecoController(); 
    
        try {
            $controller->CoariKms();
            $this->info('CoariKms executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in CoariKms: ' . $e->getMessage());
        }
    
        try {
            $controller->CodecoKms();
            $this->info('CodecoKms executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in CodecoKms: ' . $e->getMessage());
        }
    
        try {
            $controller->coariCont();
            $this->info('coariCont executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in coariCont: ' . $e->getMessage());
        }
    
        try {
            $controller->CodecoCont();
            $this->info('CodecoCont executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in CodecoCont: ' . $e->getMessage());
        }
    
        try {
            $controller->coariContFCL();
            $this->info('coariContFCL executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in coariContFCL: ' . $e->getMessage());
        }
    
        try {
            $controller->CodecoContFCL();
            $this->info('CodecoContFCL executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in CodecoContFCL: ' . $e->getMessage());
        }
    
        $this->info('All daily functions have been executed successfully!');
        \Log::info('functions:daily executed at ' . now());
    }

}
