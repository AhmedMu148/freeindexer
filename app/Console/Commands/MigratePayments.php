<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MigratePayments extends Command
{
  protected $signature = 'app:migrate-payments {--dry-run}';
  protected $description = 'Migrate payments from OLD (payments) to NEW (pym_payments) with mapping and upsert';

  public function handle()
  {
    $new = DB::connection('mysql');
    $old = DB::connection('mysql_old');

    DB::disableQueryLog();
    $count = 0;

    $old->table('payments')
      ->orderBy('id')
      ->chunk(1000, function ($rows) use ($new, &$count) {
        foreach ($rows as $r) {

          $baseDate = $r->date ?? $r->created_at ?? now();
          $created  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();
          $updated  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();

          $payload = [
            'id'              => $r->id,
            'uid'             => $r->uid,
            'plan_id'         => $r->plan_id,
            'gateway_id'      => $r->gateway_id ?? 1,
            'product'         => $r->product,
            'txn'             => $r->txn,
            'amount'          => $r->amount,
            'currency_id'     => $r->currency_id ?? 1,
            'source_details'  => $r->source_details ?? null,
            'subscription_id' => $r->subscription_id,
            'ref'             => $r->ref,
            'status'          => $r->status ?? 3,
            'created_at'      => $created,
            'updated_at'      => $updated,
          ];

          if (! $this->option('dry-run')) {
            $new->table('pym_payments')->updateOrInsert(['id' => $r->id], $payload);
          }

          $count++;
        }
      });

    $this->info("Migrated/Upserted {$count} payments.");
  }
}
