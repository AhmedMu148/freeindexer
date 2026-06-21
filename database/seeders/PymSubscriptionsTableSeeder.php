<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PymSubscriptionsTableSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('pym_subscriptions')->insert([
      'uid'         => (int) rand(1, 10),
      'gateway_id'  => 1,
      'subscr_id'   => 'sub_123456',
      'plan_id'     => 1,
      'data'        => json_encode(['renewal' => 'monthly']),
      'created_at'  => now(),
      'updated_at'  => now(),
    ]);
  }
}
