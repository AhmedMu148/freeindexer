<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UploadTempWarehouseFilesToS3 extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature  = 'warehouse:upload-temp-to-s3';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Upload files from warehouse_to_s3 to S3 and delete them locally';

  /**
   * Execute the console command.
   */

  public function handle()
  {
    $this->info('Starting upload from warehouse_to_s3 to S3...');

    $tempDisk = Storage::disk('warehouse_to_s3');
    $s3Disk   = Storage::disk('s3');

    $files = $tempDisk->allFiles();

    foreach ($files as $file) {
      $this->info("Uploading file: {$file}");

      $content = $tempDisk->get($file);

      // Use file last modified time to determine year and month
      $lastModified = Carbon::createFromTimestamp($tempDisk->lastModified($file));
      $year         = $lastModified->format('Y');
      $month        = $lastModified->format('m');

      // Build S3 path: YEAR/MONTH/file-path
      // Example: 2025/11/your-file-name.pdf
      $s3Path       = $year . '/' . $month . '/' . $file;

      // Put file to S3 (inside the bucket defined in AWS_BUCKET)
      $uploaded = $s3Disk->put($s3Path, $content);

      // $s3Path  = 'warehouse_backup/' . $file;
      // $uploaded = $s3Disk->put($s3Path, $content);

      if ($uploaded) {
        $this->info("Uploaded to S3: {$s3Path}");
        $tempDisk->delete($file);
      } else {
        $this->error("Failed to upload file to S3: {$file}");
      }
    }

    $this->info('Upload process finished.');

    return self::SUCCESS;
  }
}
