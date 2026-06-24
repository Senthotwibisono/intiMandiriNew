<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NpctController;
class npct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'functions:movementNPCT';

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
        $controller = new NpctController(); 
    
        try {
            $controller->movementInLCL();
            $this->info('sendContainer executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }
    
        try {
            $controller->movementInFCL();
            $this->info('sendContainerFCL executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainerFCL: ' . $e->getMessage());
        }
        
        try {
            $controller->movementOutLCL();
            $this->info('sendContainer executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }
    
        try {
            $controller->movementOutFCL();
            $this->info('sendContainerFCL executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainerFCL: ' . $e->getMessage());
        }

        $this->info('All daily functions have been executed successfully!');
        \Log::info('functions:daily executed at ' . now());
    }
}
