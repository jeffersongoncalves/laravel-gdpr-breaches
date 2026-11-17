<?php

namespace JeffersonGoncalves\GdprBreaches\Commands;

use Illuminate\Console\Command;
use JeffersonGoncalves\GdprBreaches\Events\BreachOverdue;
use JeffersonGoncalves\GdprBreaches\Models\Breach;

class OverdueCommand extends Command
{
    protected $signature = 'gdpr:breaches-overdue';

    protected $description = 'Dispatch BreachOverdue for every breach not notified to the supervisory authority within 72 hours';

    public function handle(): int
    {
        $count = 0;

        Breach::query()->scopes('overdue')->lazyById()->each(function (Breach $breach) use (&$count): void {
            event(new BreachOverdue($breach));
            $count++;
        });

        $count === 0
            ? $this->info('No overdue breaches.')
            : $this->warn("{$count} breach(es) past the 72-hour deadline.");

        return self::SUCCESS;
    }
}
