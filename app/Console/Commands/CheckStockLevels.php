<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckStockLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check inventory stock levels and generate alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking stock levels...');
        
        $controller = new \App\Http\Controllers\Inventory\StockAlertController();
        $response = $controller->generate();
        $data = $response->getData();
        
        if ($data->success) {
            $this->info($data->message);
            return Command::SUCCESS;
        } else {
            $this->error($data->message);
            return Command::FAILURE;
        }
    }
}
