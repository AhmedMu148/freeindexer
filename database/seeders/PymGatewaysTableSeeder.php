<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PymGatewaysTableSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('pym_gateways')->insert([
      'name'        => 'PayPal',
      'code'        => 'paypal',
      'one_time'    => true,
      'subs'        => true,
      'type'        => null,
      'minimum'     => 1.00,
      'key_id'      => null,
      'item_show'   => null,
      'data'        => null,
      'details'     => null,
      'countries'   => null,
      'increase'    => null,
      'sort'        => 1,
      'status'      => 1,
      'created_at'  => now(),
      'updated_at'  => now(),
    ]);
  }
}
