<?php

namespace JeffersonGoncalves\GdprBreaches\Tests;

use JeffersonGoncalves\GdprBreaches\GdprBreachesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            GdprBreachesServiceProvider::class,
        ];
    }
}
