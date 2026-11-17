<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use JeffersonGoncalves\GdprBreaches\Enums\Risk;
use JeffersonGoncalves\GdprBreaches\Events\BreachOpened;
use JeffersonGoncalves\GdprBreaches\Events\BreachOverdue;
use JeffersonGoncalves\GdprBreaches\Facades\GdprBreaches;
use JeffersonGoncalves\GdprBreaches\Models\Breach;

it('starts a 72-hour clock from awareness', function () {
    Event::fake([BreachOpened::class]);
    $this->freezeSecond();

    $breach = GdprBreaches::open('Backup exposed', now()->subHours(10), ['affected_subjects' => 1200]);

    expect($breach->due_at->equalTo(now()->addHours(62)))->toBeTrue()
        ->and($breach->owesAuthorityNotice())->toBeTrue()
        ->and(Breach::query()->scopes('pending')->count())->toBe(1);

    Event::assertDispatched(BreachOpened::class);
});

it('owes nothing when a risk is unlikely, but keeps the record', function () {
    $breach = GdprBreaches::assess(GdprBreaches::open('Lost encrypted laptop'), Risk::Unlikely, 'Full-disk encryption, key not exposed.');

    expect($breach->owesAuthorityNotice())->toBeFalse()
        ->and(Breach::query()->scopes('pending')->count())->toBe(0);
});

it('requires a delay reason after 72 hours', function () {
    $breach = GdprBreaches::open('Phishing');

    $this->travel(73)->hours();

    expect(fn () => GdprBreaches::notifyAuthority($breach, 'REF-1'))->toThrow(LogicException::class, 'Art. 33(1)');

    GdprBreaches::notifyAuthority($breach, 'REF-1', delayReason: 'Forensics needed to confirm personal data was involved.');

    expect($breach->fresh()->delay_reason)->not->toBeNull();
});

it('tracks a phased notice until the rest is sent', function () {
    $breach = GdprBreaches::notifyAuthority(GdprBreaches::assess(GdprBreaches::open('Ransomware'), Risk::Risk), 'REF-2', phased: true);

    expect($breach->owesFurtherInformation())->toBeTrue()
        ->and(Breach::query()->scopes('pending')->count())->toBe(1);

    GdprBreaches::sendFurtherInformation($breach);

    expect($breach->fresh()->owesFurtherInformation())->toBeFalse()
        ->and(Breach::query()->scopes('pending')->count())->toBe(0);
});

it('owes a subject notice for high risk, unless exempted', function () {
    $breach = GdprBreaches::notifyAuthority(GdprBreaches::assess(GdprBreaches::open('Passwords leaked'), Risk::High), 'REF-3');

    expect($breach->owesSubjectNotice())->toBeTrue();

    GdprBreaches::skipSubjectNotice($breach, 'Public communication on the website (Art. 34(3)(c)).');

    expect($breach->fresh()->owesSubjectNotice())->toBeFalse()
        ->and(Breach::query()->scopes('pending')->count())->toBe(0);
});

it('needs an assessment before closing', function () {
    GdprBreaches::close(GdprBreaches::open('Unknown'));
})->throws(LogicException::class, 'Assess');

it('dispatches an event per overdue breach', function () {
    Event::fake([BreachOverdue::class]);
    GdprBreaches::open('A');
    GdprBreaches::notifyAuthority(GdprBreaches::open('B'), 'REF');
    GdprBreaches::assess(GdprBreaches::open('C'), Risk::Unlikely);

    $this->travel(73)->hours();
    Artisan::call('gdpr:breaches-overdue');

    Event::assertDispatchedTimes(BreachOverdue::class, 1);
});
