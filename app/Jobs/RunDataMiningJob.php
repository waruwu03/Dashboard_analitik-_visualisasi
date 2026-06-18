<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RunDataMiningJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 600; // 10 minutes timeout

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::put('data_mining_status', 'running', now()->addMinutes(15));
        
        try {
            $exitCode = Artisan::call('data-mining:run');
            $output = mb_convert_encoding(Artisan::output(), 'UTF-8', 'UTF-8, ISO-8859-1, WINDOWS-1252');
            
            if ($exitCode === 0) {
                Cache::put('data_mining_status', 'completed', now()->addMinutes(5));
                Log::info('Data mining background job completed successfully.');
            } else {
                Cache::put('data_mining_status', 'failed', now()->addMinutes(5));
                Log::error('Data mining background job failed.', ['output' => $output]);
            }
        } catch (\Throwable $e) {
            Cache::put('data_mining_status', 'failed', now()->addMinutes(5));
            Log::error('Data mining exception in queue: ' . $e->getMessage());
        }
    }
}
