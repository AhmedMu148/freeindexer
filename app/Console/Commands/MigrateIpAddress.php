<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MigrateIpAddress extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:migrate-ip-address {--dry-run}';

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

    $ids = $new->table('ip_addresses')->pluck('id')->toArray();

    $old->table('ip_address')
      ->orderBy('id')
      ->whereNotIn('id', $ids)
      ->chunk(1000, function ($rows) use ($new, &$count) {
        foreach ($rows as $r) {

          $baseDate = $r->date ?? $r->created_at ?? now();
          $created  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();
          $updated  = $baseDate ? Carbon::parse($baseDate)->toDateTimeString() : now()->toDateTimeString();

          $payload = [
            'id'              => $r->id,
            'ip'              => $r->ip,
            'points'          => $r->points,
            'used'            => 0,
            'created_at'      => $created,
            'updated_at'      => $updated,
          ];

          if (! $this->option('dry-run')) {
            $new->table('ip_addresses')->updateOrInsert(['id' => $r->id], $payload);
          }

          $count++;
        }
      });

    $this->info("Migrated/Upserted {$count} ip_addresses.");
  }
}
