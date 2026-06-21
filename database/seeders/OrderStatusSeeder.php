<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
  public function run(): void
  {

    DB::table('order_status')->insert([
      [
        'id'          => 1,
        'name'        => 'pending',
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
      [
        'id'          => 2,
        'name'        => 'processing',
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
      [
        'id'          => 3,
        'name'        => 'completed',
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
      [
        'id'          => 4,
        'name'        => 'canceled',
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
    ]);
  }
}
