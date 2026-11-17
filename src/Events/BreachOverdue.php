<?php

namespace JeffersonGoncalves\GdprBreaches\Events;

use JeffersonGoncalves\GdprBreaches\Models\Breach;

/** The supervisory authority was not notified within 72 hours. */
class BreachOverdue
{
    public function __construct(public Breach $breach) {}
}
