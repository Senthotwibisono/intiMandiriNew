<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\JICT\GateInController;

class jict extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'functions:gateInJict';

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
        $controller = new GateInController(); 
    
        try {
            $controller->sendContainer();
            $this->info('sendContainer executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }
    
        try {
            $controller->sendContainerFCL();
            $this->info('sendContainerFCL executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainerFCL: ' . $e->getMessage());
        }

        $this->info('All daily functions have been executed successfully!');
        \Log::info('functions:daily executed at ' . now());
    }
}
