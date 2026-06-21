<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MigrateBgIndexer extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:migrate-bg-indexer {--dry-run}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $new = DB::connection('mysql');
    $old = DB::connection('mysql_old');

    DB::disableQueryLog();
    $count = 0;

    $old->table('bg_indexer')
      ->orderBy('id')
      ->chunk(1000, function ($rows) use ($new, &$count) {
        foreach ($rows as $r) {

          $baseDate = $r->date ?? $r->created_at ?? now();
          $created  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();
          $updated  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();

          $payload = [
            'id'              => $r->id,
            'uid'             => $r->uid,
            'filename'        => $r->filename,
            'urls_count'      => $r->urls_count,
            'status_id'       => $r->status,
            'created_at'      => $created,
            'updated_at'      => $updated,
          ];

          if (! $this->option('dry-run')) {
            $new->table('bg_indexer_orders')->updateOrInsert(['id' => $r->id], $payload);
          }

          $count++;
        }
      });

    $this->info("Migrated/Upserted {$count} app_free.");
  }
}
