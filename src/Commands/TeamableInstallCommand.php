<?php

namespace IBroStudio\Teamable\Commands;

use Illuminate\Console\Command;

class TeamableInstallCommand extends Command
{
    public $signature = 'teamable:install';

    public $description = 'Teamable package installer';

    public function handle(): int
    {
        $this->comment('Installing Teamable package...');

        $this->callSilently('vendor:publish', [
            '--tag' => 'teamable-config',
        ]);

        $this->callSilently('vendor:publish', [
            '--tag' => 'teamable-migrations',
        ]);

        $this->call('migrate');

        $this->info('Teamable installed');

        return self::SUCCESS;
    }
}
