<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class ResetFlag extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:reset-flag';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Reset orders flag';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $updated = Order::where('status_id', 2)->update(['flag' => 0]);
    $this->info("Reset flag for {$updated} orders.");
    return self::SUCCESS;
  }
}
