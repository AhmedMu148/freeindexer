<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Backlink;

class BacklinkSeeder extends Seeder
{
  public function run(): void
  {
    Backlink::factory()->count(10)->create();
  }
}
