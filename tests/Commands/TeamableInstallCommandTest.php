<?php

use IBroStudio\Teamable\Commands\TeamableInstallCommand;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

it('can run the install command', function () {
    Schema::dropIfExists('teams');

    artisan(TeamableInstallCommand::class)
        ->expectsOutput('Installing Teamable package...')
        ->expectsOutput('Teamable installed')
        ->assertExitCode(Command::SUCCESS);

    expect(config_path('teamable.php'))->toBeFile();
});
