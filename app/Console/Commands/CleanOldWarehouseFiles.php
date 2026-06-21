<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanOldWarehouseFiles extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'warehouse:clean-old';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Delete files older than 3 days from warehouse_secure only';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $this->info('Starting cleanup of warehouse_secure...');
    $disk             = Storage::disk('warehouse_secure');
    $files            = $disk->allFiles();
    $thresholdDelete  = Carbon::now()->subDays(3);

    foreach ($files as $file) {

      $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

      // Delete files older than 3 days
      if ($lastModified->lessThanOrEqualTo($thresholdDelete)) {
        $disk->delete($file);
        $this->warn("Deleted file older than 3 days: {$file}");
        continue;
      }

      // Delete empty files (size = 0)
      // if ($disk->size($file) === 0) {
      //   $disk->delete($file);
      //   $this->warn("Deleted empty file: {$file}");
      // }
    }

    $this->info('Cleanup finished successfully.');

    return self::SUCCESS;
  }
}
