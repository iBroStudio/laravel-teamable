<?php

namespace IBroStudio\Teamable;

use IBroStudio\Teamable\Commands\TeamableCommand;
use IBroStudio\Teamable\Models\Team;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TeamableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-teamable')
            ->hasConfigFile()
            //->hasViews()
            ->hasMigration('create_teamable_tables');
        //->hasCommand(TeamableCommand::class);
    }

    public function packageBooted()
    {
        Gate::define('switch-team', function (User $user, Team $team, User $member) {
            return $team->hasMember($member);
        });
    }
}
