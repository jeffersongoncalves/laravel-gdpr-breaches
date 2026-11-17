<?php

namespace JeffersonGoncalves\GdprBreaches\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\GdprBreaches\GdprBreaches
 */
class GdprBreaches extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-gdpr-breaches';
    }
}
