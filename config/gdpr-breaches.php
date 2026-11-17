<?php

return [

    'table' => 'gdpr_breaches',

    /*
     * Hours to notify the supervisory authority, counted from when the controller became
     * aware of the breach (GDPR Art. 33(1)). A later notice must say why it was delayed.
     */
    'deadline_hours' => 72,

];
