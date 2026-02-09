<?php

namespace App\Console\Commands;

use Database\Seeders\AffiliateDemoDataSeeder;
use Illuminate\Console\Command;

class SeedAffiliateDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate:seed-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed demo data for affiliate/commission system testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding affiliate demo data...');
        $this->newLine();

        $seeder = new AffiliateDemoDataSeeder;
        $seeder->setCommand($this);
        $seeder->run();

        $this->newLine();
        $this->info('✅ Demo data seeded successfully!');
        $this->info('Visit /affiliates to view the affiliate dashboard');

        return Command::SUCCESS;
    }
}
