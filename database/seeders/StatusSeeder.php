<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('users_status')->insert([
      [
        'id' => 1,
        'name' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id' => 2,
        'name' => 'verified',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id' => 3,
        'name' => 'suspended',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id' => 4,
        'name' => 'frozen',
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
