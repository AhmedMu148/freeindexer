<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class MigrateUsers extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:migrate-users {--dry-run}';

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


    $normalizeDate = function ($value) {
      if (empty($value)) return null;
      try {
        if (is_numeric($value)) {             // يدعم ثواني/ملّي ثانية
          $ts = (int) $value;
          if ($ts > 9999999999) $ts = (int) floor($ts / 1000);
          return Carbon::createFromTimestamp($ts)->toDateTimeString(); // "Y-m-d H:i:s"
        }
        return Carbon::parse($value)->toDateTimeString();
      } catch (\Throwable $e) {
        return null;
      }
    };

    $normalizePassword = function ($value) {
      $v = (string) $value;
      // If it's already a hash (bcrypt/argon), keep it.
      if (preg_match('#^\$(2y|2a|argon2id|argon2i)\$#', $v)) {
        return $v;
      }
      return Hash::make($v);
    };

    $ids = $new->table('users')->pluck('id')->toArray();

    $old->table('users')
      ->orderBy('id')
      ->whereNotIn('id', $ids)
      ->chunk(2000, function ($rows) use ($new, &$count, $normalizeDate, $normalizePassword) {
        foreach ($rows as $r) {

          // $dt = null;
          // try {
          //   $dt = Carbon::parse($r->date);
          // } catch (\Throwable $e) {
          // }

          // $created = $dt ? $dt->toDateTimeString() : now()->toDateTimeString(); // <-- string
          // $updated = $created;

          $data = explode(' ', $r->date);
          $date = $data[0];
          $time = $data[1];

          // echo $date;
          // echo '<br>';
          // echo $time;
          // exit;
          $created = $date . ' ' . '01:01:01';

          // $created      = $normalizeDate($r->date ?? 0);
          $lastActivity = $normalizeDate($r->lastactivity ?? 0);
          $password     = $normalizePassword($r->password ?? '');

          $payload = [
            'id'                => $r->id,
            'name'              => $r->username,
            'email'             => $r->email,
            'email_verified_at' => null,
            'password'          => $password,
            'last_activity'     => $lastActivity,      // string أو null
            'status_id'         => $r->status ?? 1,
            'app'               => $r->app ?? null,
            'api_key'           => $r->key ?? null,
            'remember_token'    => null,
            'created_at'        => $created,           // **string**
            'updated_at'        => $lastActivity,           // **string**
          ];

          // var_dump($payload);
          // exit;

          if (! $this->option('dry-run')) {
            $new->table('users')->updateOrInsert(['id' => $r->id], $payload);
          }
          // exit;
          $count++;
        }
      });

    $this->info("Migrated/Upserted {$count} app_free.");
  }
}
