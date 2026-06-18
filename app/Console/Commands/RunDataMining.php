<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class RunDataMining extends Command
{
    protected $signature = 'data-mining:run';
    protected $description = 'Run the Python data mining engine for customer segmentation and product recommendations.';

    public function handle(): int
    {
        $this->info('🚀 Starting data mining engine...');

        $script = base_path('app/DataMining/data_mining_engine.py');

        if (! file_exists($script)) {
            $this->error("Python script not found at: {$script}");
            return self::FAILURE;
        }

        // Resolve Python executable: env config → auto-detect → fallback
        $pythonBin = $this->resolvePythonExecutable();
        if (! $pythonBin) {
            $this->error('Python executable not found. Set PYTHON_EXECUTABLE in your .env file.');
            return self::FAILURE;
        }

        $this->line("  Using Python: <comment>{$pythonBin}</comment>");
        $this->line("  Script: <comment>{$script}</comment>");
        $this->newLine();

        $env = getenv();
        if (!isset($env['SystemRoot']) && isset($_SERVER['SystemRoot'])) {
            $env['SystemRoot'] = $_SERVER['SystemRoot'];
        } elseif (!isset($env['SystemRoot'])) {
            $env['SystemRoot'] = 'C:\\Windows';
        }
        
        $process = new Process([$pythonBin, $script], base_path(), $env);
        $process->setTimeout(3600); // 1-hour timeout for large datasets

        $process->run(function (string $type, string $buffer) {
            foreach (explode(PHP_EOL, trim($buffer)) as $line) {
                if ($line === '') {
                    continue;
                }
                if ($type === Process::ERR) {
                    $this->line("  <fg=yellow>[PYTHON]</> {$line}");
                } else {
                    $this->line("  <fg=cyan>[ENGINE]</> {$line}");
                }
            }
        });

        if (! $process->isSuccessful()) {
            $this->newLine();
            $this->error('❌ Data mining execution failed.');
            $errorOutput = $process->getErrorOutput() ?: $process->getOutput();
            if ($errorOutput) {
                $this->error(trim($errorOutput));
            }
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('✅ Data mining completed successfully.');
        return self::SUCCESS;
    }

    /**
     * Resolve the Python executable to use.
     * Priority: .env PYTHON_EXECUTABLE → 'python3' → 'python' → 'py' (Windows launcher)
     */
    private function resolvePythonExecutable(): ?string
    {
        $fromEnv = env('PYTHON_EXECUTABLE');
        if ($fromEnv) {
            return $fromEnv;
        }

        $finder = new ExecutableFinder();
        foreach (['python3', 'python', 'py'] as $candidate) {
            if ($finder->find($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
