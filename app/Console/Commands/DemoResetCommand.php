<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DemoResetCommand extends Command
{
    protected $signature = 'demo:reset {--force : Force reset even when running in production with demo mode enabled}';

    protected $description = 'Reset and re-seed the TemuKampus demo database and clean demo uploaded files.';

    public function handle(): int
    {
        if (! config('demo.enabled')) {
            $this->error('Demo reset is disabled because APP_DEMO_MODE is false.');

            return self::FAILURE;
        }

        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Refusing to reset demo in production without --force.');

            return self::FAILURE;
        }

        foreach (['items', 'claim-proofs'] as $directory) {
            Storage::disk('public')->deleteDirectory($directory);
        }

        $this->call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);

        $this->info('TemuKampus demo environment has been reset cleanly.');

        return self::SUCCESS;
    }
}
