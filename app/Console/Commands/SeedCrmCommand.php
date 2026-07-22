<?php

namespace App\Console\Commands;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;

class SeedCrmCommand extends Command
{
    /**
     * The name and signature of the console command.
     * Removed the --fresh flag entirely.
     */
    protected $signature = 'crm:seed';

    /**
     * The console command description.
     */
    protected $description = 'Seed the CRM roles and permissions into the database safely.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting CRM roles and permissions seeding...');

        (new RolesAndPermissionsSeeder)->run();

        $this->info('✅ CRM roles and permissions seeded successfully!');

        return Command::SUCCESS;
    }
}
