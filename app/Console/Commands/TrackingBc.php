<?php

namespace App\Console\Commands;
use App\Http\Controllers\cesa\BackController;
use Illuminate\Console\Command;

class TrackingBc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'functions:trackingBc';

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
        $controller = new BackController(); 
    
        try {
            $controller->apiTrackingIn();
            $this->info('sendContainer executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }

        try {
            $controller->apiTrackingOut();
            $this->info('sendContainer executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }

        try {
            $controller->coariCont();
            $this->info('sendContainer executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }

        try {
            $controller->codecoCont();
            $this->info('sendContainer executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }

        try {
            $controller->sppbOnDemand();
            $this->info('sendContainer executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }
    
        $this->info('All trackingBc have been executed successfully!');
        \Log::info('functions:trackingBc executed at ' . now());
    }
}
