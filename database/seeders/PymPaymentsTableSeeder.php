<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PymPaymentsTableSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('pym_payments')->insert([
      'uid'             => (int) rand(1, 10),
      'plan_id'         => 1,
      'gateway_id'      => 1,
      'product'         => 'basic',
      'txn'             => 'TXN123456',
      'amount'          => 99.99,
      'currency_id'     => 1,
      'source_details'  => json_encode(['card' => '**** **** **** 4242']),
      'subscription_id' => 1,
      'ref'             => 'REF123',
      'status'          => 'completed',
      'created_at'      => now(),
      'updated_at'      => now(),
    ]);
  }
}
