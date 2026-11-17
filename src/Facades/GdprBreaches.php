<?php

namespace JeffersonGoncalves\GdprBreaches\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\GdprBreaches\BreachManager;

/**
 * @method static \JeffersonGoncalves\GdprBreaches\Models\Breach open(string $title, ?\DateTimeInterface $discoveredAt = null, array<string, mixed> $attributes = [])
 * @method static \JeffersonGoncalves\GdprBreaches\Models\Breach assess(\JeffersonGoncalves\GdprBreaches\Models\Breach $breach, \JeffersonGoncalves\GdprBreaches\Enums\Risk $risk, ?string $measures = null)
 * @method static \JeffersonGoncalves\GdprBreaches\Models\Breach notifyAuthority(\JeffersonGoncalves\GdprBreaches\Models\Breach $breach, ?string $reference = null, ?\DateTimeInterface $at = null, ?string $delayReason = null, bool $phased = false)
 * @method static \JeffersonGoncalves\GdprBreaches\Models\Breach sendFurtherInformation(\JeffersonGoncalves\GdprBreaches\Models\Breach $breach, ?\DateTimeInterface $at = null)
 * @method static \JeffersonGoncalves\GdprBreaches\Models\Breach notifySubjects(\JeffersonGoncalves\GdprBreaches\Models\Breach $breach, ?\DateTimeInterface $at = null)
 * @method static \JeffersonGoncalves\GdprBreaches\Models\Breach skipSubjectNotice(\JeffersonGoncalves\GdprBreaches\Models\Breach $breach, string $reason)
 * @method static \JeffersonGoncalves\GdprBreaches\Models\Breach close(\JeffersonGoncalves\GdprBreaches\Models\Breach $breach, ?string $measures = null)
 * @method static \Illuminate\Support\Carbon deadline(\DateTimeInterface $discoveredAt)
 *
 * @see BreachManager
 */
class GdprBreaches extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BreachManager::class;
    }
}
