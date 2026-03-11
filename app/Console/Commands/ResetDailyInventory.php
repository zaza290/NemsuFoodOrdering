<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\MenuItem;
use App\Models\WasteLog;
use Illuminate\Support\Facades\DB;

class ResetDailyInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:reset-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset current stock to target stock and log unsold items as waste at 5:00 PM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily inventory reset and waste logging...');

        DB::transaction(function () {
            $itemsWithStock = MenuItem::where('current_stock', '>', 0)->get();
            $date = now()->toDateString();

            foreach ($itemsWithStock as $item) {
                // 1. Calculate lost profit
                $lostProfit = $item->current_stock * $item->price;

                // 2. Create Waste Log
                WasteLog::create([
                    'store_id' => $item->store_id,
                    'menu_item_id' => $item->id,
                    'quantity_wasted' => $item->current_stock,
                    'lost_profit' => $lostProfit,
                    'date' => $date,
                ]);

                // 3. Log to console
                $this->line("Logged waste for {$item->name}: {$item->current_stock} units, ₱{$lostProfit} lost.");
            }

            // 4. Reset all items back to daily target stock
            MenuItem::query()->update([
                'current_stock' => DB::raw('daily_target_stock')
            ]);
        });

        $this->info('Daily inventory reset completed successfully.');
    }
}
