<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PymGatewayTypeTableSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('pym_gateway_type')->insert([
      'name'        => 'Card',
      'created_at'  => now(),
      'updated_at'  => now(),
    ]);
  }
}
