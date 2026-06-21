<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MigrateUsersDetails extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:migrate-users-details {--dry-run}';

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

    $old->table('users_details')
      ->orderBy('id')
      ->chunk(1000, function ($rows) use ($new, &$count) {
        foreach ($rows as $r) {

          $payload = [
            'id'            => $r->id,
            'uid'           => $r->uid,
            'country_id'    => $r->country,
            'ref_url'       => $r->ref_url,
            'created_at'    => now(),
            'updated_at'    => now(),
          ];

          if (! $this->option('dry-run')) {
            $new->table('users_details')->updateOrInsert(['id' => $r->id], $payload);
          }

          $count++;
        }
      });

    $this->info("Migrated/Upserted {$count} app_free.");
  }
}
