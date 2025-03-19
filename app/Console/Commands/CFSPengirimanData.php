<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CFS\PengirimanDataCFSController;

use Auth;
use App\Models\User;
class CFSPengirimanData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'functions:cfsScheduler';

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
        Auth::login(User::find(1));

        $controller = new PengirimanDataCFSController(); 
    
        try {
            $controller->CoariKMS();
            $this->info('CoariKms CFS executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in CoariKms: ' . $e->getMessage());
        }
    
        try {
            $controller->CodecoKMS();
            $this->info('CodecoKms CFS executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in CodecoKms: ' . $e->getMessage());
        }
    
        try {
            $controller->CoariCont();
            $this->info('coariCont CFS executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in coariCont: ' . $e->getMessage());
        }
    
        try {
            $controller->detilHouseBl();
            $this->info('detilHouseBl CFS executed successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in detilHouseBl: ' . $e->getMessage());
        }
    
        $this->info('All cfsScheduler functions have been CFS executed successfully!');
        \Log::info('functions:cfsScheduler CFS executed at ' . now());

        Auth::logout();
    }
}
