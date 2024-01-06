<?php

namespace IBroStudio\Teamable\Commands;

use Illuminate\Console\Command;

class TeamableCommand extends Command
{
    public $signature = 'laravel-teamable';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
