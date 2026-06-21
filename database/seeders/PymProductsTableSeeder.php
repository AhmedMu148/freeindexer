<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PymProductsTableSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('pym_products')->insert([
      'code'        => 'basic',
      'name'        => 'Basic Plan',
      'type'        => 'subscription',
      'data'        => json_encode(['features' => ['indexing', 'support']]),
      'status'      => 'active',
      'created_at'  => now(),
      'updated_at'  => now(),
    ]);
  }
}
