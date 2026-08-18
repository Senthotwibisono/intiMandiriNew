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
            $this->info('Coari executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }

        try {
            $controller->codecoCont();
            $this->info('Codeco executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in sendContainer: ' . $e->getMessage());
        }

        try {
            $controller->sppbGet();
            $this->info('SPPB executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in SPPB: ' . $e->getMessage());
        }

        try {
            $controller->bc23Get();
            $this->info('SPPBBC23 executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in SPPBBC23: ' . $e->getMessage());
        }

        try {
            $controller->pabeanGet();
            $this->info('Pabean executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in Pabean: ' . $e->getMessage());
        }

        try {
            $controller->spjmGet();
            $this->info('SPJM executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in SPJM: ' . $e->getMessage());
        }

        try {
            $controller->plpGet();
            $this->info('PLP executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in PLP: ' . $e->getMessage());
        }
    
        $this->info('All trackingBc have been executed successfully!');
        \Log::info('functions:trackingBc executed at ' . now());
    }
}
