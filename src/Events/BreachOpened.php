<?php

namespace JeffersonGoncalves\GdprBreaches\Events;

use JeffersonGoncalves\GdprBreaches\Models\Breach;

/** Start the response: the 72-hour clock is running. */
class BreachOpened
{
    public function __construct(public Breach $breach) {}
}
