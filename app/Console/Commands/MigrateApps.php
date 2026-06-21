<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MigrateApps extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:migrate-apps {--dry-run}';

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

    $ids = $new->table('app')->pluck('id')->toArray();

    $old->table('app')
      ->orderBy('id')
      ->whereNotIn('id', $ids)
      ->chunk(1000, function ($rows) use ($new, &$count) {
        foreach ($rows as $r) {

          // $baseDate = $r->date ?? $r->created_at ?? now();
          // $created  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();
          // $updated  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();

          $data     = explode(' ', $r->date);
          $date     = $data[0];
          $created  = $date . ' ' . '01:01:01';
          $updated  = $date . ' ' . '01:01:01';

          $payload = [
            'id'              => $r->id,
            'uid'             => $r->uid,
            'payment_id'      => $r->payment_id,
            'key'             => $r->key,
            'created_at'      => $created,
            'updated_at'      => $updated,
          ];

          if (! $this->option('dry-run')) {
            $new->table('app')->updateOrInsert(['id' => $r->id], $payload);
          }

          $count++;
        }
      });

    $this->info("Migrated/Upserted {$count} app.");
  }
}
