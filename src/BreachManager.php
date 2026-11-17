<?php

namespace JeffersonGoncalves\GdprBreaches;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\GdprBreaches\Enums\Risk;
use JeffersonGoncalves\GdprBreaches\Events\BreachOpened;
use JeffersonGoncalves\GdprBreaches\Models\Breach;
use LogicException;

class BreachManager
{
    /**
     * Register a breach. The 72-hour clock starts when you became aware of it (Art. 33(1)).
     *
     * @param  array<string, mixed>  $attributes  description, occurred_at, data_categories, affected_subjects, affected_records, consequences, measures
     */
    public function open(string $title, ?DateTimeInterface $discoveredAt = null, array $attributes = []): Breach
    {
        $discoveredAt = Carbon::instance($discoveredAt ?? now());

        $breach = Breach::create([
            ...$attributes,
            'title' => $title,
            'discovered_at' => $discoveredAt,
            'due_at' => $this->deadline($discoveredAt),
        ]);

        event(new BreachOpened($breach));

        return $breach;
    }

    /**
     * Record the risk assessment. Unlikely to result in a risk: no notification owed, keep the reasoning in measures.
     */
    public function assess(Breach $breach, Risk $risk, ?string $measures = null): Breach
    {
        return $this->update($breach, ['risk' => $risk, 'measures' => $measures ?? $breach->measures]);
    }

    /**
     * Notify the supervisory authority. After the 72 hours a reason for the delay is required (Art. 33(1)).
     * Pass $phased when some information is still missing and will follow (Art. 33(4)).
     */
    public function notifyAuthority(Breach $breach, ?string $reference = null, ?DateTimeInterface $at = null, ?string $delayReason = null, bool $phased = false): Breach
    {
        $at = Carbon::instance($at ?? now());

        if ($at->isAfter($breach->due_at) && blank($delayReason)) {
            throw new LogicException("Breach [{$breach->getKey()}] is notified after 72 hours: give the reasons for the delay (Art. 33(1)).");
        }

        return $this->update($breach, [
            'authority_notified_at' => $at,
            'authority_reference' => $reference,
            'delay_reason' => $delayReason,
            'further_information_owed' => $phased,
            'further_information_at' => null,
        ]);
    }

    /** The rest of a phased notice was sent (Art. 33(4)). */
    public function sendFurtherInformation(Breach $breach, ?DateTimeInterface $at = null): Breach
    {
        if (! $breach->further_information_owed) {
            throw new LogicException("Breach [{$breach->getKey()}] has no phased notice to complete.");
        }

        return $this->update($breach, ['further_information_at' => $at ?? now()]);
    }

    public function notifySubjects(Breach $breach, ?DateTimeInterface $at = null): Breach
    {
        return $this->update($breach, ['subjects_notified_at' => $at ?? now()]);
    }

    /**
     * High risk but no communication owed: encrypted data, risk no longer likely, or a public
     * communication instead of individual ones (Art. 34(3)(a) to (c)).
     */
    public function skipSubjectNotice(Breach $breach, string $reason): Breach
    {
        return $this->update($breach, ['no_subject_notice_reason' => $reason]);
    }

    public function close(Breach $breach, ?string $measures = null): Breach
    {
        if ($breach->risk === null) {
            throw new LogicException("Assess the risk of breach [{$breach->getKey()}] before closing it.");
        }

        return $this->update($breach, ['closed_at' => now(), 'measures' => $measures ?? $breach->measures]);
    }

    public function deadline(DateTimeInterface $discoveredAt): Carbon
    {
        return Carbon::instance($discoveredAt)->addHours((int) config('gdpr-breaches.deadline_hours', 72));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function update(Breach $breach, array $attributes): Breach
    {
        $breach->forceFill($attributes)->save();

        return $breach;
    }
}
