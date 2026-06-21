<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MoveOldWarehouseFilesToTemp extends Command
{
  protected $signature = 'warehouse:move-old-to-temp';
  protected $description = 'Move files older than 2 days from warehouse_secure to warehouse_to_s3';

  public function handle()
  {
    $this->info('Starting to move old files from warehouse_secure to warehouse_to_s3...');

    $sourceDisk = Storage::disk('warehouse_secure');
    $tempDisk   = Storage::disk('warehouse_to_s3');

    $files      = $sourceDisk->allFiles();
    $threshold  = Carbon::now()->subDays(2);

    foreach ($files as $file) {

      $lastModified = Carbon::createFromTimestamp($sourceDisk->lastModified($file));

      // Condition: file must be older than 2 days
      if (! $lastModified->lessThanOrEqualTo($threshold)) {
        continue;
      }

      $this->info("Checking file: {$file}");

      // delete file if empty
      $size = $sourceDisk->size($file);

      if ($size === 0) {
        $sourceDisk->delete($file);
        $this->warn("Delete empty file: {$file}");
        continue;
      }

      // Get file content
      $content = $sourceDisk->get($file);

      // If file already exists in the temp folder, rename it to avoid overwriting
      $newFileName = $file;

      if ($tempDisk->exists($file)) {

        $fileInfo = pathinfo($file);
        $name     = $fileInfo['filename'];
        $ext      = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';

        // Generate a unique new filename
        $newFileName = $name . '_' . time() . '_' . rand(1000, 9999) . $ext;

        $this->warn("File already exists, renaming to: {$newFileName}");
      }

      // Write the file to the temp folder
      $written = $tempDisk->put($newFileName, $content);

      if ($written) {
        // Delete file from the original folder after successful write
        $sourceDisk->delete($file);
        $this->info("Moved and deleted from source: {$newFileName}");
      } else {
        $this->error("Failed to write file to temp disk: {$file}");
      }
    }

    $this->info('Move process finished.');

    return self::SUCCESS;
  }
}
