<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MigrateOrders extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:migrate-orders {--dry-run}';

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

    $ids = $new->table('orders')->pluck('id')->toArray();

    $old->table('orders')
      ->orderBy('id')
      ->whereNotIn('id', $ids)
      ->chunk(1000, function ($rows) use ($new, &$count) {
        foreach ($rows as $r) {

          $baseDate = $r->date ?? $r->created_at ?? now();
          $created  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();
          $updated  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();

          $endDate = $r->end;
          if ($r->end == '0000-00-00') {
            $endDate = null;
          }

          $payload = [
            'id'                => $r->id,
            'uid'               => $r->uid,
            'payment_id'        => $r->payment_id,
            'subscription_id'   => $r->subscription_id,
            'plan_id'           => $r->plan_id,
            'indexer'           => $r->indexer,
            'bg_indexer'        => $r->bg_indexer,
            'backlinks'         => $r->third_party,
            'start'             => $r->start,
            'end'               => $endDate,
            'status_id'         => $r->status,
            'created_at'        => $created,
            'updated_at'        => $updated,
          ];

          if (! $this->option('dry-run')) {
            $new->table('orders')->updateOrInsert(['id' => $r->id], $payload);
          }

          $count++;
        }
      });

    $this->info("Migrated/Upserted {$count} app_free.");
  }
}
