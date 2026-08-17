<?php

namespace App\Console\Commands;

use App\Models\Unit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetReservationCycle extends Command
{
    protected $signature = 'reservations:reset-cycle {--force : Skip the confirmation prompt}';

    protected $description = 'Delete all reservations and handovers and set every unit back to available (recover from the pre-fix flow).';

    public function handle(): int
    {
        $handovers = DB::table('handovers')->count();
        $reservations = DB::table('reservations')->count();
        $units = Unit::count();

        $this->warn('This will permanently:');
        $this->line("  • delete {$handovers} handover(s)");
        $this->line("  • delete {$reservations} reservation(s)");
        $this->line("  • set {$units} unit(s) back to \"available\"");
        $this->newLine();
        $this->line('Clients and sales are left untouched.');
        $this->newLine();

        if (! $this->option('force') && ! $this->confirm('This cannot be undone. Continue?')) {
            $this->info('Aborted. Nothing changed.');

            return self::SUCCESS;
        }

        DB::transaction(function () {
            // Handovers reference reservations, so clear them first.
            DB::table('handovers')->delete();
            DB::table('reservations')->delete();

            Unit::query()->update([
                'status' => Unit::STATUS_AVAILABLE,
            ]);
        });

        $this->info('Done. Reservations and handovers deleted; all units set to available.');

        return self::SUCCESS;
    }
}
