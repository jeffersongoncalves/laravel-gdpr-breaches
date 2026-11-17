<?php

namespace JeffersonGoncalves\GdprBreaches\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\GdprBreaches\Enums\Risk;

/**
 * A personal data breach (GDPR Art. 4(12)). Every breach is documented, notified or not (Art. 33(5)).
 *
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $occurred_at
 * @property Carbon $discovered_at when the controller became aware
 * @property Carbon $due_at
 * @property Risk|null $risk
 * @property list<string>|null $data_categories
 * @property int|null $affected_subjects
 * @property int|null $affected_records
 * @property string|null $consequences
 * @property string|null $measures
 * @property Carbon|null $authority_notified_at
 * @property string|null $authority_reference
 * @property string|null $delay_reason
 * @property bool $further_information_owed
 * @property Carbon|null $further_information_at
 * @property Carbon|null $subjects_notified_at
 * @property string|null $no_subject_notice_reason
 * @property Carbon|null $closed_at
 * @property Carbon $created_at
 */
class Breach extends Model
{
    protected $guarded = [];

    protected $attributes = [
        'further_information_owed' => false,
    ];

    public function getTable(): string
    {
        return config('gdpr-breaches.table', 'gdpr_breaches');
    }

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'discovered_at' => 'datetime',
            'due_at' => 'datetime',
            'risk' => Risk::class,
            'data_categories' => 'array',
            'affected_subjects' => 'integer',
            'affected_records' => 'integer',
            'authority_notified_at' => 'datetime',
            'further_information_owed' => 'boolean',
            'further_information_at' => 'datetime',
            'subjects_notified_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /** Not assessed yet, or assessed as a risk and the authority not notified. */
    public function owesAuthorityNotice(): bool
    {
        return $this->closed_at === null
            && $this->authority_notified_at === null
            && ($this->risk === null || $this->risk->requiresAuthorityNotice());
    }

    /** High risk and the data subjects neither told nor exempted (Art. 34(3)). */
    public function owesSubjectNotice(): bool
    {
        return $this->closed_at === null
            && $this->risk?->requiresSubjectNotice() === true
            && $this->subjects_notified_at === null
            && $this->no_subject_notice_reason === null;
    }

    /** A phased notice still owes the rest of the information (Art. 33(4)). */
    public function owesFurtherInformation(): bool
    {
        return $this->further_information_owed && $this->further_information_at === null;
    }

    public function isOverdue(): bool
    {
        return $this->owesAuthorityNotice() && $this->due_at->isPast();
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePending(Builder $query): void
    {
        $query->whereNull('closed_at')->where(fn (Builder $q) => $q
            ->where(fn (Builder $q) => $q->whereNull('authority_notified_at')->where(fn (Builder $q) => $q->whereNull('risk')->orWhere('risk', '!=', Risk::Unlikely->value)))
            ->orWhere(fn (Builder $q) => $q->where('risk', Risk::High->value)->whereNull('subjects_notified_at')->whereNull('no_subject_notice_reason'))
            ->orWhere(fn (Builder $q) => $q->where('further_information_owed', true)->whereNull('further_information_at')));
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeOverdue(Builder $query): void
    {
        $query->whereNull('closed_at')
            ->whereNull('authority_notified_at')
            ->where(fn (Builder $q) => $q->whereNull('risk')->orWhere('risk', '!=', Risk::Unlikely->value))
            ->where('due_at', '<', now());
    }
}
