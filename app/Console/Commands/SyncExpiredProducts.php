<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use Illuminate\Console\Command;

class SyncExpiredProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:sync-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate menu items that have passed their expiration date.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = MenuItem::where('is_available', true)
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<', now()->toDateString())
            ->update(['is_available' => false]);

        $this->info("Successfully deactivated {$count} expired items.");
    }
}
