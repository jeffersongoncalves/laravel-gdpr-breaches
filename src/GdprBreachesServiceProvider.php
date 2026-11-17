<?php

namespace JeffersonGoncalves\GdprBreaches;

use JeffersonGoncalves\GdprBreaches\Commands\OverdueCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GdprBreachesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('gdpr-breaches')
            ->hasConfigFile()
            ->hasMigrations(['create_gdpr_breaches_table'])
            ->hasCommand(OverdueCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(BreachManager::class);
    }
}
