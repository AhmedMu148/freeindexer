<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('app')->insert([
      [
        'uid'         => (int) rand(1, 10),
        'payment_id'  => Str::random(10),
        'key'         => Str::random(32),
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
      [
        'uid'         => (int) rand(1, 10),
        'payment_id'  => null,
        'key'         => Str::random(32),
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
    ]);
  }
}
