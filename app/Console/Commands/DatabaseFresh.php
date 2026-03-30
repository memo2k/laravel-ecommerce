<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseFresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:database-fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh the database and seed the data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('migrate:fresh', [
            '--seed' => true,
        ]);

        $this->info('Database fresh and seeded successfully');
    }
}
