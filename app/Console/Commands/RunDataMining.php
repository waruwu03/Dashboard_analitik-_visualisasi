<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RunDataMining extends Command
{
    protected $signature = 'data-mining:run';
    protected $description = 'Run the Python data mining engine for customer segmentation and product recommendations.';

    public function handle(): int
    {
        $this->info('Starting data mining engine...');

        $script = base_path('app/DataMining/data_mining_engine.py');
        $process = new Process(['python3', $script]);
        $process->setTimeout(3600);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Data mining execution failed.');
            $this->error($process->getErrorOutput() ?: $process->getOutput());
            return self::FAILURE;
        }

        $this->info('Data mining completed successfully.');
        $this->line($process->getOutput());
        return self::SUCCESS;
    }
}
