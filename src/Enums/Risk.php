<?php

namespace JeffersonGoncalves\GdprBreaches\Enums;

/**
 * Risk to the rights and freedoms of natural persons.
 */
enum Risk: string
{
    /** Unlikely to result in a risk: document it (Art. 33(5)), no notification owed. */
    case Unlikely = 'unlikely';

    /** Notify the supervisory authority (Art. 33(1)). */
    case Risk = 'risk';

    /** Also communicate the breach to the data subjects (Art. 34(1)). */
    case High = 'high';

    public function requiresAuthorityNotice(): bool
    {
        return $this !== self::Unlikely;
    }

    public function requiresSubjectNotice(): bool
    {
        return $this === self::High;
    }
}
