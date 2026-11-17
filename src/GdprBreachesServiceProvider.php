<?php

namespace JeffersonGoncalves\GdprBreaches;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GdprBreachesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-gdpr-breaches')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
